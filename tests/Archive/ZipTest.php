<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Archive;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Archive\ArchiveInfo;
use Platine\DocxTemplate\Archive\Zip;
use Platine\DocxTemplate\Exception\DocxArchiveException;
use ZipArchive;

/**
 * Zip class tests
 *
 * @group core
 * @group docx-template
 */
class ZipTest extends PlatineTestCase
{

    public function testConstructorDefault(): void
    {
        $l = new Zip();
        $this->assertInstanceOf(ZipArchive::class, $this->getPropertyValue(Zip::class, $l, 'zip'));
    }

    public function testConstructorCustomZipArchive(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class);
        $l = new Zip($zipArchive);
        $this->assertInstanceOf(ZipArchive::class, $this->getPropertyValue(Zip::class, $l, 'zip'));
        $this->assertEquals($zipArchive, $this->getPropertyValue(Zip::class, $l, 'zip'));
    }

    public function testArchiveFolderFailed(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => false
        ]);
        $l = new Zip($zipArchive);
        $this->expectException(DocxArchiveException::class);
        $l->archiveFolder('.', 'foo.zip');
    }

    public function testArchiveFolderSuccess(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => true
        ]);

        $zipArchive->expects($this->atLeast(1))
                    ->method('addFile');

        $zipArchive->expects($this->exactly(1))
                    ->method('close');

        $l = new Zip($zipArchive);
        $l->archiveFolder(sys_get_temp_dir(), 'foo.zip');
    }

    public function testExtractOpenFailed(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => false
        ]);
        $l = new Zip($zipArchive);
        $this->expectException(DocxArchiveException::class);
        $l->extract('foo.zip');
    }

    public function testExtractFailed(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => true,
            'extractTo' => false,
        ]);
        $l = new Zip($zipArchive);
        $this->expectException(DocxArchiveException::class);
        $l->extract('foo.zip');
    }

    public function testTotalFiles(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => true
        ]);


        $l = new Zip($zipArchive);
        $this->assertEquals(0, $l->totalFiles());
    }

    public function testClose(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'open' => true
        ]);

        $zipArchive->expects($this->exactly(1))
                    ->method('close');


        $l = new Zip($zipArchive);
        $l->close();
    }

    public function testGetInfoFailed(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'statIndex' => false,
        ]);
        $l = new Zip($zipArchive);
        $this->expectException(DocxArchiveException::class);
        $l->getInfo(0);
    }

    public function testGetInfoSuccess(): void
    {
        $zipArchive = $this->getMockInstance(ZipArchive::class, [
            'statIndex' => [
                'name' => 'foo',
                'index' => 1,
                'size' => 2,
                'mtime' => 3,
                'crc' => 4,
            ],
        ]);
        $l = new Zip($zipArchive);
        $info = $l->getInfo(0);
        $this->assertInstanceOf(ArchiveInfo::class, $info);
        $this->assertEquals('foo', $info->getName());
        $this->assertEquals(1, $info->getIndex());
        $this->assertEquals(2, $info->getSize());
        $this->assertEquals(3, $info->getMtime());
        $this->assertEquals(4, $info->getCrc());
    }
}
