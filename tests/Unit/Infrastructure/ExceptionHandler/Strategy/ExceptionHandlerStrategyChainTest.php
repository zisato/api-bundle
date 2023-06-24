<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\ExceptionHandler\Strategy;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyChain;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyChain
 */
class ExceptionHandlerStrategyChainTest extends TestCase
{
    /** @var ExceptionHandlerStrategyInterface|MockObject $strategy1 */
    private $strategy1;
    /** @var ExceptionHandlerStrategyInterface|MockObject $strategy2 */
    private $strategy2;
    private ExceptionHandlerStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy1 = $this->createMock(ExceptionHandlerStrategyInterface::class);
        $this->strategy2 = $this->createMock(ExceptionHandlerStrategyInterface::class);

        $this->strategy = new ExceptionHandlerStrategyChain($this->strategy1, $this->strategy2);
    }

    public function testCanHandleTrue(): void
    {
        $this->strategy1->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $this->strategy2->expects($this->once())
            ->method('canHandle')
            ->willReturn(true);

        $result = $this->strategy->canHandle(new Exception());

        $this->assertTrue($result);
    }

    public function testCanHandleFalse(): void
    {
        $this->strategy1->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $this->strategy2->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $result = $this->strategy->canHandle(new Exception());

        $this->assertFalse($result);
    }

    public function testHandle(): void
    {
        $exception = new Exception();

        /** @var Response|MockObject $response */
        $response = $this->createMock(Response::class);

        $this->strategy1->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $this->strategy2->expects($this->once())
            ->method('canHandle')
            ->willReturn(true);

        $this->strategy2->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($exception))
            ->willReturn($response);

        $result = $this->strategy->handle($exception);

        $this->assertEquals($response, $result);
    }

    public function testHandleException(): void
    {
        $this->expectException(RuntimeException::class);
        
        $exception = new Exception();

        $this->strategy1->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $this->strategy2->expects($this->once())
            ->method('canHandle')
            ->willReturn(false);

        $this->strategy->handle($exception);
    }
}
