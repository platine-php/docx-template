<?php

declare(strict_types=1);

namespace Platine\Test\DocxTemplate\Convertor;

use Platine\Dev\PlatineTestCase;
use Platine\DocxTemplate\Convertor\LibreOfficePDFConvertor;
use RuntimeException;

/**
 * LibreOfficePDFConvertor class tests
 *
 * @group core
 * @group docx-template
 */
class LibreOfficePDFConvertorTest extends PlatineTestCase
{

    public function testPassthruNotExists(): void
    {
        global $mock_function_exists_to_false;

        $mock_function_exists_to_false = true;

        $l = new LibreOfficePDFConvertor();
        $this->expectException(RuntimeException::class);
        $this->assertEquals('foo', $l->convert('foo'));
    }

    public function testPassthruExitCodeError(): void
    {
        global $mock_function_exists_to_true,
               $mock_passthru_to_exitcode_error;

        $mock_function_exists_to_true = true;
        $mock_passthru_to_exitcode_error = true;

        $l = new LibreOfficePDFConvertor();
        $this->expectException(RuntimeException::class);
        $this->assertEquals('foo', $l->convert('foo'));
    }

    public function testSuccess(): void
    {
        global $mock_function_exists_to_true,
               $mock_passthru_to_empty;

        $mock_function_exists_to_true = true;
        $mock_passthru_to_empty = true;

        $l = new LibreOfficePDFConvertor();
        $this->assertEquals('/mnt/foo.pdf', $l->convert('/mnt/foo'));
    }
}
