<?php

namespace Zisato\ApiBundle\Infrastructure\Opis\JsonSchema;

use JsonException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Helper;
use Opis\JsonSchema\Validator;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaValidatorException;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;

final class JsonSchemaValidator implements JsonSchemaValidatorInterface
{
    public function __construct(private readonly Validator $validator) {}

    public function validateData(string $json, string $jsonSchema): array
    {
        try {
            $data = \json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new JsonSchemaValidatorException(\sprintf('Invalid json. Error: %s', $jsonException->getMessage()));
        }

        $result = $this->validator->validate(Helper::toJSON($data), $jsonSchema);

        if ($result->hasError()) {
            $formatter = new ErrorFormatter();
            throw new JsonSchemaValidatorException(\sprintf(
                'Invalid json schema. Message: %s',
                $formatter->formatErrorMessage($result->error())
            ));
        }

        return $data;
    }
}
