<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Convertor\NullConvertor;
use Platine\DocxTemplate\DocxTemplate;
use Platine\DocxTemplate\Renderer\NullRenderer;
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
}
