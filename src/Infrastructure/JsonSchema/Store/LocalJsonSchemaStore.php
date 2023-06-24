<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\JsonSchema\Store;

use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaStoreException;

final class LocalJsonSchemaStore implements JsonSchemaStoreInterface
{
    public function __construct(private readonly string $jsonSchemaPath) {}

    public function get(string $schemaName): string
    {
        $filename = $this->jsonSchemaPath . $schemaName;

        if (! \is_file($filename)) {
            throw new JsonSchemaStoreException(\sprintf('File %s not exists', $filename));
        }

        $content = @\file_get_contents($filename);

        if ($content === false) {
            $error = \error_get_last();

            throw new JsonSchemaStoreException(\sprintf(
                'Could not get file content of %s. Error: %s',
                $filename,
                $error['message'] ?? 'undefined'
            ));
        }

        return $content;
    }
}
