<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Archive\ArchiveInfo;
use Platine\DocxTemplate\Archive\NullExtractor;
use Platine\DocxTemplate\Convertor\LibreOfficePDFConvertor;
use Platine\DocxTemplate\Convertor\NullConvertor;
use Platine\DocxTemplate\DocxTemplate;
use Platine\DocxTemplate\Renderer\NullRenderer;
use Platine\DocxTemplate\Renderer\PlatineTemplateRenderer;
use Platine\Filesystem\Adapter\Local\Directory;
use Platine\Filesystem\Adapter\Local\File;
use Platine\Filesystem\Filesystem;

/**
 * DocxTemplate class tests
 *
 * @group core
 * @group docx-template
 */
class DocxTemplateTest extends PlatineTestCase
{
    public function testConstructorDefault(): void
    {
        $convertor = $this->getMockInstance(NullConvertor::class);
        $renderer = $this->getMockInstance(NullRenderer::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, $renderer, $convertor);
        $this->assertInstanceOf(NullConvertor::class, $l->getConvertor());
        $this->assertInstanceOf(NullRenderer::class, $l->getRenderer());
        $this->assertEquals($convertor, $l->getConvertor());
        $this->assertEquals($renderer, $l->getRenderer());
    }

    public function testGetConvertor(): void
    {
        $convertor = $this->getMockInstance(LibreOfficePDFConvertor::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, null, $convertor);
        $this->assertEquals($convertor, $l->getConvertor());
        $this->assertInstanceOf(LibreOfficePDFConvertor::class, $l->getConvertor());
    }

    public function testGetRenderer(): void
    {
        $renderer = $this->getMockInstance(PlatineTemplateRenderer::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, $renderer);
        $this->assertEquals($renderer, $l->getRenderer());
        $this->assertInstanceOf(PlatineTemplateRenderer::class, $l->getRenderer());
    }

    public function testGetExtractor(): void
    {
        $extractor = $this->getMockInstance(NullExtractor::class);
        $convertor = $this->getMockInstance(NullConvertor::class);
        $renderer = $this->getMockInstance(PlatineTemplateRenderer::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, $renderer, $convertor, $extractor);
        $this->assertEquals($extractor, $l->getExtractor());
        $this->assertInstanceOf(NullExtractor::class, $l->getExtractor());
    }

    public function testSetGetData(): void
    {
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem);
        $this->assertEmpty($l->getData());
        $l->setData(['foo' => 'bar']);
        $data = $l->getData();
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
    }

    public function testSetTemplateFile(): void
    {
        $extractor = $this->getMockInstance(NullExtractor::class);
        $convertor = $this->getMockInstance(NullConvertor::class);
        $renderer = $this->getMockInstance(PlatineTemplateRenderer::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, $renderer, $convertor, $extractor);
        $l->setTemplateFile('mytemplate.docx');
        $this->assertEquals('mytemplate.docx', $this->getPropertyValue(
            DocxTemplate::class,
            $l,
            'templateFile'
        ));
    }

    public function testSetTempDir(): void
    {
        $extractor = $this->getMockInstance(NullExtractor::class);
        $convertor = $this->getMockInstance(NullConvertor::class);
        $renderer = $this->getMockInstance(PlatineTemplateRenderer::class);
        $filesystem = $this->getMockInstance(Filesystem::class);

        $l = new DocxTemplate($filesystem, $renderer, $convertor, $extractor);
        $l->setTempDir('tmp');
        $this->assertEquals('tmp', $this->getPropertyValue(
            DocxTemplate::class,
            $l,
            'tempDir'
        ));
    }

    public function testProcessAndConvert(): void
    {
        $templateDir = $this->createVfsDirectory('tests');
        $tempDir = $this->createVfsDirectory('tests_tmp');
        $templateFile = $this->createVfsFile('template.docx', $templateDir, 'foocontent');
        $mockFileCopy = $this->getMockInstance(File::class, [
            'getPath' => $templateFile->url()
        ]);
        $mockFile = $this->getMockInstance(File::class, [
            'copyTo' => $mockFileCopy,
            'exists' => true,
        ]);
        $mockExtractDir = $this->getMockInstance(Directory::class, [
            'getPath' => $templateFile->url()
        ]);
        $mockDir = $this->getMockInstance(Directory::class, [
            'create' => $mockExtractDir
        ]);

        $filesystem = $this->getMockInstance(Filesystem::class, [
            'file' => $mockFile,
            'directory' => $mockDir,
        ]);

        $archiveInfo = $this->getMockInstance(ArchiveInfo::class, [
            'getName' => 'word/document.xml'
        ]);

        $extractor = $this->getMockInstance(NullExtractor::class, [
            'getInfo' => $archiveInfo,
            'totalFiles' => 10,
        ]);
        $convertor = new NullConvertor();
        $renderer = $this->getMockInstance(PlatineTemplateRenderer::class);

        $l = new DocxTemplate($filesystem, $renderer, $convertor, $extractor);
        $l->setTemplateFile($templateFile->url())
            ->setTempDir($tempDir->url())
            ->process();
        $this->assertEquals(
            $tempDir->url() . '/output_template.docx',
            $l->getOutputTemplateFile()
        );

        $l->convert();
        $this->assertEquals(
            $tempDir->url() . '/output_template.docx',
            $l->getConversionFile()
        );
    }
}
