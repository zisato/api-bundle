<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\ExceptionHandler\Strategy;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ValidationErrorExceptionHandler;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ValidationErrorExceptionHandler
 */
class ValidationErrorExceptionHandlerTest extends TestCase
{
    /** @var ResponseServiceInterface|MockObject $responseService */
    private $responseService;
    private ExceptionHandlerStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->responseService = $this->createMock(ResponseServiceInterface::class);

        $this->strategy = new ValidationErrorExceptionHandler($this->responseService);
    }

    /**
     * @dataProvider getCanHandleData
     */
    public function testCanHandle(Exception $exception, bool $expected): void
    {
        $result = $this->strategy->canHandle($exception);

        $this->assertEquals($expected, $result);
    }

    public function testHandle(): void
    {
        /** @var Response|MockObject $response */
        $response = $this->createMock(Response::class);

        $this->responseService->expects($this->once())
            ->method('respondValidationError')
            ->willReturn($response);
        
        $result = $this->strategy->handle(new Exception());

        $this->assertEquals($response, $result);
    }

    public static function getCanHandleData(): array
    {
        return [
            [
                new InvalidArgumentException(),
                true
            ],
            [
                new Exception(),
                false
            ]
        ];
    }
}
