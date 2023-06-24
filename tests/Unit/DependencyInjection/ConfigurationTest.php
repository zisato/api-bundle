<?php

namespace Zisato\ApiBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Zisato\ApiBundle\DependencyInjection\Configuration;
use Zisato\ApiBundle\Infrastructure\ExceptionHandler\Strategy\ExceptionHandlerStrategyInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /*
    Not compatible with phpunit 10
    
    public function testJsonSchemaPathInvalidValues(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'json_schema_path' => null,
                ]
            ],
            'json_schema_path'
        );
    }
    
    public function testApiProblemExceptionHandlersInvalidValues(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'api_problem' => [
                        'exception_handlers' => [
                            'foo'
                        ],
                    ]
                ]
            ],
            'api_problem.exception_handlers'
        );
    }

    */

    public function testProcessConfigurationDefaultValues(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                []
            ],
            [
                'json_schema_path' => '%kernel.project_dir%/public/schemas/',
                'api_problem' => [
                    'enabled' => true,
                    'exception_handlers' => [],
                ]
            ]
        );
    }

    public function testProcessUsedConfigurationValues(): void
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


        $this->assertProcessedConfigurationEquals(
            [
                [
                    'json_schema_path' => 'custom path',
                    'api_problem' => [
                        'enabled' => false,
                        'exception_handlers' => [
                            $exceptionHandler::class
                        ],
                    ]
                ]
            ],
            [
                'json_schema_path' => 'custom path',
                'api_problem' => [
                    'enabled' => false,
                    'exception_handlers' => [
                        $exceptionHandler::class
                    ],
                ]
            ]
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
