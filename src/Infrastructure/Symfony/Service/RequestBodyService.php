<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Symfony\Service;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\JsonSchemaStoreInterface;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Validator\JsonSchemaValidatorInterface;
use Zisato\ApiBundle\Infrastructure\Service\RequestBodyServiceInterface;

final class RequestBodyService implements RequestBodyServiceInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly JsonSchemaStoreInterface $jsonSchemaStore,
        private readonly JsonSchemaValidatorInterface $jsonSchemaValidator
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function requestBody(string $schemaName): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new InvalidArgumentException('Request cannot be null.');
        }

        $jsonSchema = $this->jsonSchemaStore->get($schemaName);

        /** @var string $requestContent */
        $requestContent = $request->getContent();

        return $this->jsonSchemaValidator->validateData($requestContent, $jsonSchema);
    }
}
