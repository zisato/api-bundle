<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\Symfony\Service;

use PHPUnit\Framework\TestCase;
use Zisato\ApiBundle\Infrastructure\APIProblem\APIProblem;
use Zisato\ApiBundle\Infrastructure\Symfony\Service\ResponseService;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\Symfony\Service\ResponseService
 */
class ResponseServiceTest extends TestCase
{
    private ResponseService $responseService;

    protected function setUp(): void
    {
        $this->responseService = new ResponseService();
    }

    /**
     * @dataProvider getRespondSuccessData
     */
    public function testRespondSuccess(?array $data, int $expectedCode, string $expectedContent): void
    {
        $result = $this->responseService->respondSuccess($data);

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondCollection(): void
    {
        $data = [];
        $totalItems = 42;
        $page = 1;
        $perPage = 12;
        $totalPages = 3;
        $expectedCode = 200;
        $expectedContent = json_encode([
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'total' => $totalItems,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => $totalPages,
                ],
            ]
        ]);

        $result = $this->responseService->respondCollection($data, $totalItems, $page, $perPage, $totalPages);

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondCreated(): void
    {
        $expectedCode = 201;
        $expectedContent = '';

        $result = $this->responseService->respondCreated();

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondUpdated(): void
    {
        $expectedCode = 204;
        $expectedContent = '';

        $result = $this->responseService->respondUpdated();

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondDeleted(): void
    {
        $expectedCode = 204;
        $expectedContent = '';

        $result = $this->responseService->respondDeleted();

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondBadRequest(): void
    {
        $expectedCode = 400;
        $expectedContent = '{"status":400,"type":"about:blank","title":"Bad Request"}';

        $result = $this->responseService->respondBadRequest();

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    /**
     * @dataProvider getRespondNotFoundData
     */
    public function testRespondNotFound(?string $message, string $expectedContent): void
    {
        $expectedCode = 404;

        $result = $this->responseService->respondNotFound($message);

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public function testRespondUnauthorized(): void
    {
        $expectedCode = 401;
        $expectedContent = '{"status":401,"type":"about:blank","title":"Unauthorized"}';

        $result = $this->responseService->respondUnauthorized();

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    /**
     * @dataProvider getRespondValidationErrorData
     */
    public function testRespondValidationError(?string $message, string $expectedContent): void
    {
        $expectedCode = 400;

        $result = $this->responseService->respondValidationError($message);

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    /**
     * @dataProvider getRespondErrorData
     */
    public function testRespondError(?APIProblem $APIProblem, int $expectedCode, string $expectedContent): void
    {
        $result = $this->responseService->respondError($APIProblem);

        $this->assertEquals($expectedCode, $result->getStatusCode());
        $this->assertEquals($expectedContent, $result->getContent());
    }

    public static function getRespondSuccessData(): array
    {
        return [
            [
                null,
                200,
                ''
            ],
            [
                ['foo' => 'bar'],
                200,
                '{"data":{"foo":"bar"}}'
            ]
        ];
    }

    public static function getRespondNotFoundData(): array
    {
        return [
            [
                null,
                '{"status":404,"type":"about:blank","title":"Not Found"}'
            ],
            [
                'Not found message',
                '{"status":404,"type":"about:blank","title":"Not Found"}'
            ]
        ];
    }

    public static function getRespondValidationErrorData(): array
    {
        return [
            [
                null,
                '{"status":400,"type":"about:blank","title":"Bad Request"}'
            ],
            [
                'Validation error message',
                '{"status":400,"type":"about:blank","title":"Bad Request"}'
            ]
        ];
    }

    public static function getRespondErrorData(): array
    {
        return [
            [
                null,
                500,
                '{"status":500,"type":"about:blank","title":"Internal Server Error"}'
            ],
            [
                new APIProblem(442),
                442,
                '{"status":442,"type":"about:blank","title":"Unknown error"}'
            ]
        ];
    }
}
