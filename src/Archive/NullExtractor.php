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
 *  @file NullExtractor.php
 *
 *  The Null extractor class
 *
 *  @package    Platine\DocxTemplate\Archive
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   http://www.iacademy.cf
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\DocxTemplate\Archive;

use Platine\DocxTemplate\DocxExtractorInterface;

/**
 * @class NullExtractor
 * @package Platine\DocxTemplate\Archive
 */
class NullExtractor implements DocxExtractorInterface
{
    /**
     * {@inheritodc}
     */
    public function archiveFolder(string $folder, string $filename): void
    {
    }

    /**
     * {@inheritodc}
     */
    public function extract(string $archive, string $destination = '.'): void
    {
    }

    /**
     * {@inheritodc}
     */
    public function totalFiles(): int
    {
        return 0;
    }

    /**
     * {@inheritodc}
     */
    public function close(): void
    {
    }

    /**
     * {@inheritodc}
     */
    public function getInfo(int $index): ArchiveInfo
    {
        return new ArchiveInfo(
            '',
            0,
            0,
            0,
            0
        );
    }
}
