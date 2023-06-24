<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Symfony\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Zisato\ApiBundle\Infrastructure\APIProblem\APIProblem;
use Zisato\ApiBundle\Infrastructure\Service\ResponseServiceInterface;

final class ResponseService implements ResponseServiceInterface
{
    public function respondSuccess(?array $data = null): JsonResponse
    {
        if ($data !== null) {
            $data = [
                'data' => $data,
            ];
        }

        return $this->createResponse(Response::HTTP_OK, $data);
    }

    public function respondCollection(
        array $data,
        int $totalItems,
        int $page,
        int $perPage,
        int $totalPages
    ): JsonResponse {
        $data = [
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'total' => $totalItems,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => $totalPages,
                ],
            ],
        ];

        return $this->createResponse(Response::HTTP_OK, $data);
    }

    public function respondCreated(): JsonResponse
    {
        return $this->createResponse(Response::HTTP_CREATED);
    }

    public function respondUpdated(): JsonResponse
    {
        return $this->createResponse(Response::HTTP_NO_CONTENT);
    }

    public function respondDeleted(): JsonResponse
    {
        return $this->createResponse(Response::HTTP_NO_CONTENT);
    }

    public function respondBadRequest(): JsonResponse
    {
        return $this->createAPIProblemResponse(Response::HTTP_BAD_REQUEST);
    }

    public function respondNotFound(?string $message = null): JsonResponse
    {
        return $this->createAPIProblemResponse(Response::HTTP_NOT_FOUND, $message);
    }

    public function respondUnauthorized(): JsonResponse
    {
        return $this->createAPIProblemResponse(Response::HTTP_UNAUTHORIZED);
    }

    public function respondValidationError(?string $message = null): JsonResponse
    {
        return $this->createAPIProblemResponse(Response::HTTP_BAD_REQUEST, $message);
    }

    public function respondError(?APIProblem $APIProblem = null): JsonResponse
    {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $title = null;

        if ($APIProblem instanceof APIProblem) {
            $code = $APIProblem->status();
            $title = $APIProblem->title();
        }

        return $this->createAPIProblemResponse($code, $title);
    }

    /**
     * @param array<string|int, mixed>|null $data
     */
    protected function createResponse(int $statusCode, ?array $data = null): JsonResponse
    {
        $response = new JsonResponse($data, $statusCode);

        if ($data === null) {
            $response->setContent(null);
        }

        return $response;
    }

    protected function createAPIProblemResponse(int $status, ?string $title = null): JsonResponse
    {
        $apiProblem = new APIProblem($status, null, $title);

        return $this->createResponse($apiProblem->status(), $apiProblem->toArray());
    }
}
