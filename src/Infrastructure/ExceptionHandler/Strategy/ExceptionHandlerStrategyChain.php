<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ExceptionHandlerStrategyChain implements ExceptionHandlerStrategyInterface
{
    /**
     * @var array<ExceptionHandlerStrategyInterface>
     */
    private readonly array $exceptionHandlerStrategies;

    public function __construct(ExceptionHandlerStrategyInterface ...$exceptionHandlerStrategies)
    {
        $this->exceptionHandlerStrategies = $exceptionHandlerStrategies;
    }

    public function canHandle(Throwable $exception): bool
    {
        foreach ($this->exceptionHandlerStrategies as $strategy) {
            if ($strategy->canHandle($exception)) {
                return true;
            }
        }

        return false;
    }

    public function handle(Throwable $exception): Response
    {
        foreach ($this->exceptionHandlerStrategies as $strategy) {
            if ($strategy->canHandle($exception)) {
                return $strategy->handle($exception);
            }
        }

        throw new RuntimeException(sprintf('No strategies defined for exception %s', get_class($exception)));
    }
}
