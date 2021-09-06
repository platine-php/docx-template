<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Renderer;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Renderer\NullRenderer;

/**
 * NullRenderer class tests
 *
 * @group core
 * @group docx-template
 */
class NullRendererTest extends PlatineTestCase
{

    public function testDefault(): void
    {
        $l = new NullRenderer();
        $this->assertEquals('foo', $l->render('foo', []));
    }
}
