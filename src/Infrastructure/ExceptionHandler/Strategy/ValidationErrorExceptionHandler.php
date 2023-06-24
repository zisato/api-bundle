<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

final class ValidationErrorExceptionHandler implements ExceptionHandlerStrategyInterface
{
    public function __construct(private readonly ResponseServiceInterface $responseService) {}

    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof InvalidArgumentException;
    }

    public function handle(Throwable $exception): Response
    {
        return $this->responseService->respondValidationError();
    }
}
