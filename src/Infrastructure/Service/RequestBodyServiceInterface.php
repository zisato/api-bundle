<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Service;

interface RequestBodyServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function requestBody(string $schemaName): array;
}
