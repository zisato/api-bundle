<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\Symfony\Service;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\JsonSchemaStoreInterface;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;
use Zisato\ApiBundle\Infrastructure\Service\RequestBodyServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\RequestBodyService;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\Symfony\Service\RequestBodyService
 */
class RequestBodyServiceTest extends TestCase
{
    /** @var RequestStack|MockObject $requestStack */
    private $requestStack;
    /** @var JsonSchemaStoreInterface|MockObject $jsonSchemaStore */
    private $jsonSchemaStore;
    /** @var JsonSchemaValidatorInterface|MockObject $jsonSchemaValidator */
    private $jsonSchemaValidator;
    private RequestBodyServiceInterface $requestBodyService;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->jsonSchemaStore = $this->createMock(JsonSchemaStoreInterface::class);
        $this->jsonSchemaValidator = $this->createMock(JsonSchemaValidatorInterface::class);

        $this->requestBodyService = new RequestBodyService($this->requestStack, $this->jsonSchemaStore, $this->jsonSchemaValidator);
    }

    public function testRequestBody(): void
    {
        $schemaName = 'testSchemaName.json';
        $requestContent = '{}';
        $jsonSchema = '{}';
        $expected = [];

        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->jsonSchemaStore->expects($this->once())
            ->method('get')
            ->with($this->equalTo($schemaName))
            ->willReturn($jsonSchema);

        $this->jsonSchemaValidator->expects($this->once())
            ->method('validateData')
            ->with($this->equalTo($requestContent), $this->equalTo($jsonSchema))
            ->willReturn($expected);

        $result = $this->requestBodyService->requestBody($schemaName);

        $this->assertEquals($expected, $result);
    }

    public function testRequestBodyExceptionWhenNullCurrentRequest(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $schemaName = 'testSchemaName.json';

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->requestBodyService->requestBody($schemaName);
    }
}
