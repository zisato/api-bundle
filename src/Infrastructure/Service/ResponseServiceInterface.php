<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\Service;

use Zisato\ApiBundle\Infrastructure\APIProblem\APIProblem;

interface ResponseServiceInterface
{
    /**
     * @param array<string|int, mixed>|null $data
     */
    public function respondSuccess(?array $data = null);

    /**
     * @param array<string|int, mixed> $data
     */
    public function respondCollection(array $data, int $totalItems, int $page, int $perPage, int $totalPages);

    public function respondCreated();

    public function respondUpdated();

    public function respondDeleted();

    public function respondBadRequest();

    public function respondNotFound();

    public function respondUnauthorized();

    public function respondValidationError();

    public function respondError(?APIProblem $APIProblem = null);
}
