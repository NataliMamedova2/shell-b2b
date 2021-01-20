<?php

namespace Tests\Unit\Clients\Infrastructure\Document\Service;

use App\Clients\Infrastructure\Document\Service\UploadDocumentFileService;
use FilesUploader\File\PathGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class UploadDocumentFileServiceTest extends TestCase
{
    /**
     * @var PathGeneratorInterface|ObjectProphecy
     */
    private $pathGeneratorMock;
    /**
     * @var FilesystemInterface|ObjectProphecy
     */
    private $filesystemInterfaceMock;
    /**
     * @var UploadDocumentFileService
     */
    private $service;

    protected function setUp(): void
    {
        $this->pathGeneratorMock = $this->prophesize(PathGeneratorInterface::class);
        $this->filesystemInterfaceMock = $this->prophesize(FilesystemInterface::class);

        $this->service = new UploadDocumentFileService($this->pathGeneratorMock->reveal(), $this->filesystemInterfaceMock->reveal());
    }

    public function testUploadEmptyResourceReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $namePrefix = 'prefix';
        $extension = 'ext';
        $this->service->upload(null, $namePrefix, $extension);
    }

    public function testUploadWriteStreamFalseReturnException(): void
    {
        $resource = tmpfile();
        $namePrefix = 'prefix';
        $extension = 'ext';
        $nameWithExtension = $namePrefix.'_'.time().'.'.$extension;

        $path = 'path/test/';
        $this->pathGeneratorMock->generate($nameWithExtension, ['pathPrefix' => 'documents'])
            ->shouldBeCalled()
            ->willReturn($path);
        $this->filesystemInterfaceMock->writeStream($path.$nameWithExtension, $resource)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->expectException(\RuntimeException::class);

        $this->service->upload($resource, $namePrefix, $extension);
    }

    public function testUploadWriteStreamTrueReturnFileObject(): void
    {
        $resource = tmpfile();
        $namePrefix = 'prefix';
        $extension = 'ext';
        $name = $namePrefix.'_'.time();
        $nameWithExtension = $name.'.'.$extension;

        $path = 'path/test/';
        $this->pathGeneratorMock->generate($nameWithExtension, ['pathPrefix' => 'documents'])
            ->shouldBeCalled()
            ->willReturn($path);
        $this->filesystemInterfaceMock->writeStream($path.$nameWithExtension, $resource)
            ->shouldBeCalled()
            ->willReturn(true);

        $result = $this->service->upload($resource, $namePrefix, $extension);

        $this->assertEquals($name, $result->getName());
        $this->assertEquals($extension, $result->getExtension());
        $this->assertEquals($path.$nameWithExtension, $result->getFile());
    }
}
