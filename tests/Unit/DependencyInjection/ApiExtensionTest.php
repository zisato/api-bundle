<?php

namespace Zisato\ApiBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Opis\JsonSchema\Validator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Zisato\ApiBundle\DependencyInjection\ApiExtension;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\ExceptionHandlerServiceInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\BadRequestExceptionHandler;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyChain;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ValidationErrorExceptionHandler;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\JsonSchemaStoreInterface;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\LocalJsonSchemaStore;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;
use Zisato\ApiBundle\Infrastructure\Opis\JsonSchema\JsonSchemaValidator;
use Zisato\ApiBundle\Infrastructure\Service\ArrayKeysParserService;
use Zisato\ApiBundle\Infrastructure\Service\RequestBodyServiceInterface;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;
use Zisato\ApiBundle\Infrastructure\Symfony\EventListener\APIProblemExceptionListener;
use Zisato\ApiBundle\Infrastructure\Symfony\ExceptionHandler\ExceptionHandlerService;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\RequestBodyService;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\ResponseService;

class ApiExtensionTest extends AbstractExtensionTestCase
{
    public function testParameterJsonSchemaPathIsSet(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('api_bundle.json_schema_path', '%kernel.project_dir%/public/schemas/');
    }

    /**
     * @dataProvider getServiceExistsData
     */
    public function testServiceIdIsSetWhenDefaultConfiguration(string $serviceId): void
    {
        $this->load();

        $this->assertContainerBuilderHasService($serviceId);
    }

    /**
     * @dataProvider getServiceAliasExistsData
     */
    public function testServiceAliasIsSet(string $serviceAlias): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias($serviceAlias);
    }

    /**
     * @dataProvider getApiProblemDisabledData
     */
    public function testApiProblemDisabledNotCreateDefinitions(string $serviceId): void
    {
        $this->load([
            'api_problem' => [
                'enabled' => false,
            ],
        ]);

        $this->assertContainerBuilderNotHasService($serviceId);
    }

    public function testLoadCustomExceptionHandler(): void
    {
        $exceptionHandler = new class implements ExceptionHandlerStrategyInterface {
            public function canHandle(Throwable $exception): bool
            {
                return true;
            }

            public function handle(Throwable $exception): Response
            {
                return new Response();
            }
        };

        $this->load([
            'api_problem' => [
                'exception_handlers' => [
                    $exceptionHandler::class
                ]
            ]
        ]);

        $expectedArgument = new Reference($exceptionHandler::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(ExceptionHandlerStrategyInterface::class, 0, $expectedArgument);
    }

    public static function getServiceExistsData(): array
    {
        return [
            [
                BadRequestExceptionHandler::class,
            ],
            [
                ValidationErrorExceptionHandler::class,
            ],
            [
                ExceptionHandlerStrategyChain::class,
            ],
            [
                ExceptionHandlerService::class,
            ],
            [
                LocalJsonSchemaStore::class,
            ],
            [
                Validator::class,
            ],
            [
                JsonSchemaValidator::class,
            ],
            [
                ArrayKeysParserService::class,
            ],
            [
                RequestBodyService::class,
            ],
            [
                ResponseService::class,
            ],
            [
                APIProblemExceptionListener::class,
            ],
        ];
    }

    public static function getServiceAliasExistsData(): array
    {
        return [
            [
                ExceptionHandlerStrategyInterface::class,
            ],
            [
                ExceptionHandlerServiceInterface::class,
            ],
            [
                JsonSchemaStoreInterface::class,
            ],
            [
                JsonSchemaValidatorInterface::class,
            ],
            [
                RequestBodyServiceInterface::class,
            ],
            [
                ResponseServiceInterface::class,
            ],
        ];
    }

    public static function getApiProblemDisabledData(): array
    {
        return [
            [
                BadRequestExceptionHandler::class,
            ],
            [
                ValidationErrorExceptionHandler::class,
            ],
            [
                ExceptionHandlerStrategyChain::class,
            ],
            [
                ExceptionHandlerService::class,
            ],
            [
                APIProblemExceptionListener::class,
            ]
        ];
    }

    protected function getContainerExtensions(): array
    {
        return array(
            new ApiExtension()
        );
    }
}
