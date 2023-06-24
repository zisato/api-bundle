<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\JsonSchema\Validator;

interface JsonSchemaValidatorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function validateData(string $json, string $jsonSchema): array;
}
