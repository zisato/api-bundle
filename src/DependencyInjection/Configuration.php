<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ParentNodeDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('api_bundle');

        /** @var ParentNodeDefinitionInterface $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('api_problem')
                    ->info('Use api problem exception listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->arrayNode('exception_handlers')
                            ->info('Exception handlers strategies for your custom exceptions')
                            ->defaultValue([])
                            ->scalarPrototype()
                                ->validate()
                                    ->ifTrue(static function ($value): bool {
                                        return ! is_a($value, ExceptionHandlerStrategyInterface::class, true);
                                    })
                                    ->thenInvalid(sprintf('exception_handlers must implement %s', ExceptionHandlerStrategyInterface::class))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                
                ->scalarNode('json_schema_path')
                    ->info('The default path used to load json schemas')
                    ->defaultValue('%kernel.project_dir%/public/schemas/')
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
