<?php

namespace Zisato\ApiBundle\Tests\Infrastructure\Opis\JsonSchema;

use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaValidatorException;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;
use Zisato\ApiBundle\Infrastructure\Opis\JsonSchema\JsonSchemaValidator;

class JsonSchemaValidatorTest extends TestCase
{
    /** @var Validator|MockObject $validator */
    private $validator;
    private JsonSchemaValidatorInterface $jsonSchemaValidator;
    
    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        
        $this->jsonSchemaValidator = new JsonSchemaValidator(
            $this->validator
        );
    }

    public function testItShouldValidateDataSuccessfully()
    {
        $schemaName = 'test.json';
        $json = '{"test": "foo"}';
        $expected = [
            'test' => 'foo',
        ];
        
        $schemaValidationResult = new ValidationResult(null);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($schemaValidationResult);
        
        $result = $this->jsonSchemaValidator->validateData($json, $schemaName);
        
        $this->assertEquals($expected, $result);
    }

    public function testItShouldThrowJsonSchemaValidatorExceptionWhenInvalidJson()
    {
        $this->expectException(JsonSchemaValidatorException::class);
        
        $schemaName = 'test.json';
        
        $this->jsonSchemaValidator->validateData(' {', $schemaName);
    }

    public function testItShouldThrowInvalidJsonSchemaExceptionWhenInvalidSchema()
    {
        $this->expectException(JsonSchemaValidatorException::class);
        
        $jsonSchema = '{"type": "object", "required": ["data"], "properties": {"data": { "type": "string" } } }';

        $schemaValidationResult = $this->createMock(ValidationResult::class);
        $schemaValidationError = $this->createMock(ValidationError::class);
        
        $schemaValidationResult->expects($this->once())
            ->method('hasError')
            ->willReturn(true);

        $schemaValidationResult->expects($this->once())
            ->method('error')
            ->willReturn($schemaValidationError);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($schemaValidationResult);
        
        $this->jsonSchemaValidator->validateData('{"foo": "bar"}', $jsonSchema);
    }
}
