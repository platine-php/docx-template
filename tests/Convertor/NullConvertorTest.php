<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Convertor;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Convertor\NullConvertor;

/**
 * NullConvertor class tests
 *
 * @group core
 * @group docx-template
 */
class NullConvertorTest extends PlatineTestCase
{

    public function testDefault(): void
    {
        $l = new NullConvertor();
        $this->assertEquals('foo', $l->convert('foo'));
    }
}
