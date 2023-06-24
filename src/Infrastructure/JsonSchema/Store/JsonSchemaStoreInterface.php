<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\JsonSchema\Store;

interface JsonSchemaStoreInterface
{
    public function get(string $schemaName): string;
}
