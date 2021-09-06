<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Archive;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Archive\ArchiveInfo;
use Platine\DocxTemplate\Archive\NullExtractor;

/**
 * NullExtractor class tests
 *
 * @group core
 * @group docx-template
 */
class NullExtractorTest extends PlatineTestCase
{


    public function testArchiveFolder(): void
    {
        $l = new NullExtractor();
        $l->archiveFolder('foo', 'foo.zip');
        $this->assertTrue(true);
    }

    public function testExtract(): void
    {
        $l = new NullExtractor();
        $l->extract('foo.zip');
        $this->assertTrue(true);
    }

    public function testTotalFiles(): void
    {
        $l = new NullExtractor();
        $this->assertEquals(0, $l->totalFiles());
    }

    public function testClose(): void
    {
        $l = new NullExtractor();
        $l->close();
        $this->assertTrue(true);
    }


    public function testGetInfo(): void
    {
        $l = new NullExtractor();
        $info = $l->getInfo(0);
        $this->assertInstanceOf(ArchiveInfo::class, $info);
        $this->assertEquals('', $info->getName());
        $this->assertEquals(0, $info->getIndex());
        $this->assertEquals(0, $info->getSize());
        $this->assertEquals(0, $info->getMtime());
        $this->assertEquals(0, $info->getCrc());
    }
}
