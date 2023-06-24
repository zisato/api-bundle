<?php

namespace Zisato\ApiBundle\Tests\Unit\Infrastructure\JsonSchema\Store;

use org\bovigo\vfs\content\FileContent;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Exception\JsonSchemaStoreException;
use Zisato\ApiBundle\Infrastructure\JsonSchema\Store\LocalJsonSchemaStore;

/**
 * @covers \Zisato\ApiBundle\Infrastructure\JsonSchema\Store\LocalJsonSchemaStore
 */
class LocalJsonSchemaStoreTest extends TestCase
{
    /** @var vfsStreamDirectory $fileSystem */
    private $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = vfsStream::setup();
    }

    public function testGet(): void
    {
        $schemaName = 'test.json';
        $jsonSchemaPath = vfsStream::url('root') . '/';
        $expected = '{}';

        vfsStream::newFile($schemaName)->at($this->fileSystem)->setContent($expected);

        $store = new LocalJsonSchemaStore($jsonSchemaPath);

        $schema = $store->get($schemaName);

        $this->assertEquals($expected, $schema);
    }

    public function testJsonSchemaStoreExceptionWhenFileNotExists(): void
    {
        $this->expectException(JsonSchemaStoreException::class);

        $jsonSchemaPath = vfsStream::url('root') . '/';
        $schemaName = 'test.json';

        $store = new LocalJsonSchemaStore($jsonSchemaPath);

        $store->get($schemaName);
    }

    public function testJsonSchemaStoreExceptionWhenContentFalse(): void
    {
        $this->expectException(JsonSchemaStoreException::class);

        $jsonSchemaPath = vfsStream::url('root') . '/';
        $schemaName = 'test.json';
        
        vfsStream::newFile($schemaName, 0)->at($this->fileSystem);

        $store = new LocalJsonSchemaStore($jsonSchemaPath);

        $store->get($schemaName);
    }
}
