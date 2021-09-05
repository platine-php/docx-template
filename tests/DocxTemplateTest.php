<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\DocxTemplate;
use Platine\DocxTemplate\Renderer\PlatineTemplateRenderer;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\Filesystem;
use Platine\Template\Template;

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

    /*
    public function testConstructorCustomInstances(): void
    {
        $localAdapter = new LocalAdapter();
        $filesystem = new Filesystem($localAdapter);

        $template = new Template();

        $l = new DocxTemplate($filesystem, new PlatineTemplateRenderer($template));
        $l->setTempDir('/mnt/d/tnhdocx')
           ->setTemplateFile('/mnt/d/my_template.docx')
           ->setData([
               'header' => 'PLATINE APPLICATION',
               'footer' => 'Copyright 2021, Tony NGUEREZA',
               'name' => 'Tony NGUEREZA',
               'items' => [
                   [
                       'id' => 1,
                       'name' => 'Savon',
                       'price' => 1050,
                       'total' => 1050,
                   ],
                   [
                       'id' => 2,
                       'name' => 'Sucre',
                       'price' => 500,
                       'total' => 1000,
                   ],
                   [
                       'id' => 3,
                       'name' => 'Omo',
                       'price' => 150,
                       'total' => 1500,
                   ]
               ],
           ]);
        $l->process();
        $this->assertTrue(true);
    }

     */
}
