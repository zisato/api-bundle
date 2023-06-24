<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Symfony\ExceptionHandler;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

final class ExceptionHandlerService implements ExceptionHandlerServiceInterface
{
    public function __construct(
        private readonly ResponseServiceInterface $responseService,
        private readonly ExceptionHandlerStrategyInterface $exceptionHandlerStrategy
    ) {}

    public function handle(Throwable $exception): Response
    {
        if ($this->exceptionHandlerStrategy->canHandle($exception)) {
            return $this->exceptionHandlerStrategy->handle($exception);
        }

        return $this->responseService->respondError();
    }
}
