<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\ApiProblem;

use PHPUnit\Framework\TestCase;
use Zisato\ApiBundle\Infrastructure\APIProblem\APIProblem;

class APIProblemTest extends TestCase
{
    /**
     * @dataProvider getSuccessData
     */
    public function testCreateSuccessfully(
        int $status,
        ?string $type,
        ?string $title,
        ?string $detail,
        ?string $instance,
        ?array $extensions,
        ?string $expectedType,
        ?string $expectedTitle,
        array $expectedArray
    ) {
        $model = new APIProblem($status, $type, $title, $detail, $instance, $extensions);

        static::assertEquals($status, $model->status());
        static::assertEquals($expectedType, $model->type());
        static::assertEquals($expectedTitle, $model->title());
        static::assertEquals($detail, $model->detail());
        static::assertEquals($instance, $model->instance());
        static::assertEquals($extensions ?? [], $model->extensions());
        static::assertEquals($expectedArray, $model->toArray());
    }

    public static function getSuccessData(): array
    {
        return [
            [
                400,
                null,
                null,
                null,
                null,
                null,
                'about:blank',
                'Bad Request',
                [
                    'status' => 400,
                    'type' => 'about:blank',
                    'title' => 'Bad Request',
                ],
            ],
            [
                442,
                'my:type',
                'My Title',
                'My awesome detail',
                'my:instance',
                [
                    'my-extension' => 'extension-value'
                ],
                'my:type',
                'My Title',
                [
                    'status' => 442,
                    'type' => 'my:type',
                    'title' => 'My Title',
                    'detail' => 'My awesome detail',
                    'instance' => 'my:instance',
                    'my-extension' => 'extension-value'
                ],
            ],
        ];
    }
}
