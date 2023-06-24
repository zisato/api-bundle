<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface ExceptionHandlerStrategyInterface
{
    public function canHandle(Throwable $exception): bool;

    public function handle(Throwable $exception): Response;
}
