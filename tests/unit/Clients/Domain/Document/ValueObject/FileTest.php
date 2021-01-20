<?php

namespace Tests\Unit\Clients\Domain\Document\ValueObject;

use App\Clients\Domain\Document\ValueObject\File;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    public function testConstructValidDataReturnObject(): void
    {
        $path = 'path/to/file/';
        $fileName = 'filename';
        $fileExt = 'xls';

        $result = new File($path, $fileName, $fileExt);

        $this->assertEquals($fileName, $result->getName());
        $this->assertEquals($fileExt, $result->getExtension());
        $this->assertEquals($path.$fileName.'.'.$fileExt, $result->getFile());
    }

    public function testConstructPathNoSlashReturnObject(): void
    {
        $path = 'path_to_file';
        $fileName = 'filename';
        $fileExt = 'xls';

        $result = new File($path, $fileName, $fileExt);

        $this->assertEquals($fileName, $result->getName());
        $this->assertEquals($fileExt, $result->getExtension());
        $this->assertEquals($path.'/'.$fileName.'.'.$fileExt, $result->getFile());
    }

    public function testConstructNameWithExtReturnObject(): void
    {
        $path = 'path/to/file/';
        $fileName = 'filename.xls';
        $fileExt = 'xls';

        $result = new File($path, $fileName, $fileExt);

        $this->assertEquals('filename', $result->getName());
        $this->assertEquals($fileExt, $result->getExtension());
        $this->assertEquals($path.'filename.'.$fileExt, $result->getFile());
    }

    /**
     * @dataProvider emptyDataProvider
     *
     * @param $path
     * @param $fileName
     * @param $ext
     */
    public function testConstructEmptyDataReturnObject($path, $fileName, $ext): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new File($path, $fileName, $ext);
    }

    public function emptyDataProvider()
    {
        return [
            'empty path' => ['', 'filename.ext', 'ext'],
            'empty name' => ['path/to/file/', '', 'ext'],
            'empty ext' => ['path/to/file/', 'filename.ext', ''],
        ];
    }

}
