<?php

/**
 * Platine Docx template
 *
 * Platine Docx template is the lightweight library to manipulate the content of .docx files
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2020 Platine Docx template
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *  @file LibreOfficePDFConvertor.php
 *
 *  The Libre office document to PDF convertor class
 *
 *  @package    Platine\DocxTemplate\Convertor
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\DocxTemplate\Convertor;

use Platine\DocxTemplate\DocxConvertorInterface;
use RuntimeException;

/**
 * @class LibreOfficePDFConvertor
 * @package Platine\DocxTemplate\Convertor
 */
class LibreOfficePDFConvertor implements DocxConvertorInterface
{
    /**
     * {@inheritdoc}
     * @example:
     * sudo apt-get install libreoffice-common libreoffice-writer \
     *  default-jre libreoffice-java-common openjdk-8-jre-headless
     *
     * soffice --headless  --convert-to pdf /path/to/*.docx --outdir /path/to/out
     */
    public function convert(string $templateFile): string
    {
        if (!function_exists('passthru')) {
            throw new RuntimeException(sprintf(
                'Can not convert document using [%s], function '
                    . '"passthru" is not available in your php installation',
                __CLASS__
            ));
        }
        /** @var array<string, string> $fileinfo */
        $fileinfo = pathinfo($templateFile);
        $destinationPath = $fileinfo['dirname'];
        $convertFile = $fileinfo['filename'] . '.pdf';
        $cmd = sprintf(
            'soffice --headless  --convert-to pdf %s --outdir %s',
            escapeshellarg($templateFile),
            escapeshellarg($destinationPath)
        );
        $exitCode = 0;
        passthru($cmd, $exitCode);
        if ($exitCode !== 0) {
            throw new RuntimeException(sprintf(
                'Can not convert document to PDF, command execution error: [%d]',
                $exitCode
            ));
        }

        return $destinationPath . '/' . $convertFile;
    }
}
