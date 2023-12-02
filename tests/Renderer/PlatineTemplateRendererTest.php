<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Renderer;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Renderer\PlatineTemplateRenderer;
use Platine\Template\Template;

/**
 * PlatineTemplateRenderer class tests
 *
 * @group core
 * @group docx-template
 */
class PlatineTemplateRendererTest extends PlatineTestCase
{
    public function testConstructor(): void
    {
        $template = $this->getMockInstance(Template::class);
        $l = new PlatineTemplateRenderer($template);
        $this->assertInstanceOf(Template::class, $this->getPropertyValue(
            PlatineTemplateRenderer::class,
            $l,
            'template'
        ));

        $this->assertEquals($template, $this->getPropertyValue(
            PlatineTemplateRenderer::class,
            $l,
            'template'
        ));
    }

    public function testRender(): void
    {
        $template = $this->getMockInstance(Template::class, [
            'renderString' => 'bar'
        ]);
        $l = new PlatineTemplateRenderer($template);

        $this->assertEquals('bar', $l->render('{{ foo<w:t> h </w:t>}} {% for<w:t> h </w:t>%}', []));
    }

    public function testHandleTableLoop(): void
    {
        $template = $this->getMockInstance(Template::class, [
            'renderString' => 'bar'
        ]);
        $xml = '<w:tr><w:tc><w:t>{% for i in data %}{{i.id}}</w:t></w:tc><w:tc>'
                . '<w:t>{{i.name}}</w:t></w:tc></w:tr><w:tr><w:tc>'
                . '{% endfor %}</w:tc></w:tr>';
        $l = new PlatineTemplateRenderer($template);

        $expected = '{% for i in data %}<w:tr><w:tc><w:t>{{i.id}}'
                . '</w:t></w:tc><w:tc><w:t>{{i.name}}</w:t></w:tc></w:tr>{% endfor %}';
        $result = $this->runPrivateProtectedMethod($l, 'handleTableLoop', [$xml]);

        $this->assertEquals($expected, $result);
    }
}
