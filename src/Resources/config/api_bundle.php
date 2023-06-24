<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\RequestStack;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\JsonSchemaStoreInterface;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\LocalJsonSchemaStore;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;
use Zisato\ApiBundle\Infrastructure\Opis\JsonSchema\JsonSchemaValidator;
use Zisato\ApiBundle\Infrastructure\Service\ArrayKeysParserService;
use Zisato\ApiBundle\Infrastructure\Service\RequestBodyServiceInterface;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\RequestBodyService;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\ResponseService;

return static function (ContainerConfigurator $container): void {
    $container->services()

        // JsonSchema
        ->set(LocalJsonSchemaStore::class, LocalJsonSchemaStore::class)
        ->args(['%api_bundle.json_schema_path%'])
        ->alias(JsonSchemaStoreInterface::class, LocalJsonSchemaStore::class)

        // Opis
        ->set(Validator::class, Validator::class)

        ->set(JsonSchemaValidator::class, JsonSchemaValidator::class)
        ->args([service(Validator::class)])
        ->alias(JsonSchemaValidatorInterface::class, JsonSchemaValidator::class)

        // Service
        ->set(ArrayKeysParserService::class, ArrayKeysParserService::class)

        // Symfony
        ->set(RequestBodyService::class, RequestBodyService::class)
        ->args([
            service(RequestStack::class),
            service(JsonSchemaStoreInterface::class),
            service(JsonSchemaValidatorInterface::class),
        ])
        ->alias(RequestBodyServiceInterface::class, RequestBodyService::class)

        ->set(ResponseService::class, ResponseService::class)
        ->alias(ResponseServiceInterface::class, ResponseService::class)
    ;
};
