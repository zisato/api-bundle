<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\BadRequestExceptionHandler;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyChain;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ValidationErrorExceptionHandler;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\EventListener\APIProblemExceptionListener;
use Zisato\ApiBundle\Infrastructure\Symfony\ExceptionHandler\ExceptionHandlerService;

final class ApiExtension extends ConfigurableExtension
{
    /**
     * @var string
     */
    private const SYMFONY_KERNEL_EVENT_LISTENER = 'kernel.event_listener';

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('api_bundle.php');

        $container->setParameter('api_bundle.json_schema_path', $mergedConfig['json_schema_path']);

        if ($mergedConfig['api_problem']['enabled']) {
            $this->registerApiProblemExceptionListener($container, $mergedConfig['api_problem']['exception_handlers']);
        }
    }

    private function registerApiProblemExceptionListener(ContainerBuilder $container, array $customExceptionHandlers): void
    {
        $container
            ->register(BadRequestExceptionHandler::class, BadRequestExceptionHandler::class)
            ->setArguments([
                new Reference(ResponseServiceInterface::class)    
            ])
        ;

        $container
            ->register(ValidationErrorExceptionHandler::class, ValidationErrorExceptionHandler::class)
            ->setArguments([
                new Reference(ResponseServiceInterface::class)    
            ])
        ;

        $container
            ->register(ExceptionHandlerStrategyChain::class, ExceptionHandlerStrategyChain::class)
            ->setArguments([
                new Reference(BadRequestExceptionHandler::class),
                new Reference(ValidationErrorExceptionHandler::class),
            ])
        ;
        $container->setAlias(ExceptionHandlerStrategyInterface::class, ExceptionHandlerStrategyChain::class);
        
        $container
            ->register(ExceptionHandlerService::class, ExceptionHandlerService::class)
            ->setArguments([
                new Reference(ResponseServiceInterface::class),
                new Reference(ExceptionHandlerStrategyInterface::class),
            ])
        ;
        $container->setAlias(ExceptionHandlerServiceInterface::class, ExceptionHandlerService::class);

        $container
            ->register(APIProblemExceptionListener::class, APIProblemExceptionListener::class)
            ->setArguments([
                new Reference(ExceptionHandlerServiceInterface::class),
            ])
            ->addTag(
                self::SYMFONY_KERNEL_EVENT_LISTENER,
                [
                    'event' => 'kernel.exception',
                    'method' => 'onKernelException',
                ]
            )
        ;

        $this->mergeExceptionHandlerConfig($container, $customExceptionHandlers);
    }

    private function mergeExceptionHandlerConfig(ContainerBuilder $container, array $exceptionHandlers): void
    {
        if ($exceptionHandlers === []) {
            return;
        }

        $configArguments = array_map(static function (string $id): Reference {
            return new Reference($id);
        }, $exceptionHandlers);

        $definition = $container->findDefinition(ExceptionHandlerStrategyChain::class);

        $newArguments = array_merge($configArguments, $definition->getArguments());

        $definition->setArguments($newArguments);
    }
}
