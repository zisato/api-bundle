<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\Symfony\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\EventListener\APIProblemExceptionListener;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\Symfony\EventListener\APIProblemExceptionListener
 */
class APIProblemExceptionListenerTest extends TestCase
{
    /** @var ExceptionHandlerServiceInterface|MockObject $exceptionHandler */
    private $exceptionHandler;
    private APIProblemExceptionListener $listener;
    
    protected function setUp(): void
    {
        $this->exceptionHandler = $this->createMock(ExceptionHandlerServiceInterface::class);

        $this->listener = new APIProblemExceptionListener($this->exceptionHandler);
    }

    public function testOnKernelException(): void
    {
        $throwable = new \Exception();

        $event = $this->mockExceptionEvent($throwable);

        $response = $this->createMock(Response::class);

        $this->exceptionHandler->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($throwable))
            ->willReturn($response);

        $this->listener->onKernelException($event);
    }

    private function mockExceptionEvent(Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        
        $request = $this->createMock(Request::class);
        
        $requestType = HttpKernelInterface::MAIN_REQUEST;

        return new ExceptionEvent(
            $kernel,
            $request,
            $requestType,
            $exception
        );
    }
}
