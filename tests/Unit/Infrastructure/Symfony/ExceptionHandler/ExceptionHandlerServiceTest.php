<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\Symfony\ExceptionHandler;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\ExceptionHandler\ExceptionHandlerService;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\Symfony\ExceptionHandler\ExceptionHandlerService
 */
class ExceptionHandlerServiceTest extends TestCase
{
    /** @var ResponseServiceInterface|MockObject $responseService */
    private $responseService;
    /** @var ExceptionHandlerStrategyInterface|MockObject $exceptionHandlerStrategy */
    private $exceptionHandlerStrategy;
    private ExceptionHandlerServiceInterface $exceptionHandler;
    
    protected function setUp(): void
    {
        $this->responseService = $this->createMock(ResponseServiceInterface::class);
        $this->exceptionHandlerStrategy = $this->createMock(ExceptionHandlerStrategyInterface::class);

        $this->exceptionHandler = new ExceptionHandlerService($this->responseService, $this->exceptionHandlerStrategy);
    }

    public function testHandle(): void
    {
        $exception = new Exception();
        $response = $this->createMock(Response::class);

        $this->exceptionHandlerStrategy->expects($this->once())
            ->method('canHandle')
            ->with($this->equalTo($exception))
            ->willReturn(true);

        $this->exceptionHandlerStrategy->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($exception))
            ->willReturn($response);

        $this->exceptionHandler->handle($exception);
    }

    public function testHandleWithoutStrategy(): void
    {
        $exception = new Exception();
        $response = $this->createMock(Response::class);

        $this->exceptionHandlerStrategy->expects($this->once())
            ->method('canHandle')
            ->with($this->equalTo($exception))
            ->willReturn(false);

        $this->responseService->expects($this->once())
            ->method('respondError')
            ->willReturn($response);

        $this->exceptionHandler->handle($exception);
    }
}
