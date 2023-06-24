<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\ExceptionHandler\Strategy;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\BadRequestExceptionHandler;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaStoreException;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaValidatorException;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\BadRequestExceptionHandler
 */
class BadRequestExceptionHandlerTest extends TestCase
{
    /** @var ResponseServiceInterface|MockObject $responseService */
    private $responseService;
    private ExceptionHandlerStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->responseService = $this->createMock(ResponseServiceInterface::class);

        $this->strategy = new BadRequestExceptionHandler($this->responseService);
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
            ->method('respondBadRequest')
            ->willReturn($response);
        
        $result = $this->strategy->handle(new Exception());

        $this->assertEquals($response, $result);
    }

    public static function getCanHandleData(): array
    {
        return [
            [
                new JsonSchemaStoreException(),
                true
            ],
            [
                new JsonSchemaValidatorException(),
                true
            ],
            [
                new Exception(),
                false
            ]
        ];
    }
}
