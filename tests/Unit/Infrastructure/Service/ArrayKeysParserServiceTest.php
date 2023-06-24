<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\Service;

use PHPUnit\Framework\TestCase;
use Zisato\ApiBundle\Infrastructure\Service\ArrayKeysParserService;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\Service\ArrayKeysParserService
 */
class ArrayKeysParserServiceTest extends TestCase 
{
    /**
     * @dataProvider getValidData
     */
    public function testItShouldParseKeysSuccessfully(array $expected, array $values)
    {
        $actual = ArrayKeysParserService::arrayKeysAsDotNotation($values);
        
        $this->assertEquals($expected, $actual);
    }
    
    public static function getValidData(): array
    {
        return [
            [
                [
                    'foo',
                    'foo.bar',
                    'foo.bar.doe',
                    'foo.qwerty',
                    'poi',
                ],
                [
                    'foo' => [
                        'bar' => [
                            'doe' => 'doe'
                        ],
                        'qwerty' => 'qwerty'
                    ],
                    'poi' => 'poi',
                ],
            ]
        ];
    }
}
