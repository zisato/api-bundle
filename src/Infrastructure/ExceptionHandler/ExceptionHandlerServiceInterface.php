<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\ExceptionHandler;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface ExceptionHandlerServiceInterface
{
    public function handle(Throwable $exception): Response;
}
