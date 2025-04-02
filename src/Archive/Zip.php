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
 *  @file Zip.php
 *
 *  The Zip Archive manipulation class
 *
 *  @package    Platine\DocxTemplate\Archive
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\DocxTemplate\Archive;

use FilesystemIterator;
use Platine\DocxTemplate\DocxExtractorInterface;
use Platine\DocxTemplate\Exception\DocxArchiveException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * @class Zip
 * @package Platine\DocxTemplate\Archive
 */
class Zip implements DocxExtractorInterface
{
    /**
     * The zip archive instance
     * @var ZipArchive
     */
    protected ZipArchive $zip;

    /**
     * Create new instance
     * @param ZipArchive|null $zip
     */
    public function __construct(?ZipArchive $zip = null)
    {
        $this->zip = $zip ?? new ZipArchive();
    }

    /**
     * {@inheritdoc}
     */
    public function archiveFolder(string $folder, string $filename): void
    {
        $create = $this->zip->open(
            $filename,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );
        if ($create === true) {
             // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $folder,
                    FilesystemIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                //NOTE: Always use "/" as separator
                $name = str_replace(
                    ['/', '\\'],
                    '/',
                    substr($name, strlen($folder . '/'))
                );
                $this->zip->addFile($file->getRealPath(), $name);
            }
            $this->zip->close();
        } else {
            throw new DocxArchiveException(sprintf(
                'Can not open file [%s], error code [%s]',
                $filename,
                $create
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function extract(string $archive, string $destination = '.'): void
    {
        $open = $this->zip->open($archive);
        if ($open !== true) {
            throw new DocxArchiveException(sprintf(
                'Can not open file [%s], error code [%s]',
                $archive,
                $open
            ));
        }

        if (!$this->zip->extractTo($destination)) {
            throw new DocxArchiveException(sprintf(
                'Can not extract file [%s]',
                $archive
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function totalFiles(): int
    {
        return $this->zip->count();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->zip->close();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(int $index): ArchiveInfo
    {
        $stat = $this->zip->statIndex($index);
        if ($stat === false) {
            throw new DocxArchiveException(sprintf(
                'Can not stat the archive file at index [%d]',
                $index
            ));
        }

        return new ArchiveInfo(
            $stat['name'],
            $stat['index'],
            $stat['size'],
            $stat['mtime'],
            $stat['crc']
        );
    }
}
