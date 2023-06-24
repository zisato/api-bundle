<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaStoreException;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaValidatorException;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

final class BadRequestExceptionHandler implements ExceptionHandlerStrategyInterface
{
    public function __construct(private readonly ResponseServiceInterface $responseService) {}

    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof JsonSchemaStoreException ||
            $exception instanceof JsonSchemaValidatorException;
    }

    public function handle(Throwable $exception): Response
    {
        return $this->responseService->respondBadRequest();
    }
}
