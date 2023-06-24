<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Service;

final class ArrayKeysParserService
{
    /**
     * @param array<string|int, mixed> $array
     * @return array<string|int, mixed>
     */
    public static function arrayKeysAsDotNotation(array $array): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($array),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $path = [];
        $dotKeys = [];

        foreach ($iterator as $key => $value) {
            $path[$iterator->getDepth()] = $key;

            $dotKeys[] = \implode('.', \array_slice($path, 0, $iterator->getDepth() + 1));
        }

        return $dotKeys;
    }
}
