<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Symfony\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;

final class APIProblemExceptionListener
{
    public function __construct(private readonly ExceptionHandlerServiceInterface $exceptionHandlerService) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $event->setResponse($this->exceptionHandlerService->handle($event->getThrowable()));
    }
}
