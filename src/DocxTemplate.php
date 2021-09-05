<?php

/**
 * Platine Docx template
 *
 * Platine Docx template is the lightweight library to manipulate the content
 * of .docx files
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
 *  @file DocxTemplate.php
 *
 *  The Docx Template main class.
 *
 *  @package    Platine\DocxTemplate
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   http://www.iacademy.cf
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\DocxTemplate;

use FilesystemIterator;
use Platine\DocxTemplate\Convertor\NullConvertor;
use Platine\DocxTemplate\Exception\DocxTemplateException;
use Platine\DocxTemplate\Renderer\NullRenderer;
use Platine\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * @class DocxTemplate
 * @package Platine\DocxTemplate
 */
class DocxTemplate
{

    /**
     * The convertor instance to use
     * @var DocxConvertorInterface
     */
    protected DocxConvertorInterface $convertor;

    /**
     * The template renderer instance
     * @var DocxTemplateRendererInterface
     */
    protected DocxTemplateRendererInterface $renderer;

    /**
     * The file instance to use
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * The data to use to replace template variables
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * The document template file
     * @var string
     */
    protected string $templateFile;

    /**
     * The template temporary file path
     * @var string
     */
    protected string $templateTempFile;

    /**
     * Output template file after renderer
     * @var string
     */
    protected string $outputTemplateFile;

    /**
     * The file path after conversion
     * @var string
     */
    protected string $convertionFile = '';

    /**
     * The list of files inside document template
     * @var array<string>
     */
    protected array $docxFileList = [];

    /**
     * The list of files pattern to use
     * @var array<string>
     */
    protected array $fileListToProcess = [
        'word/document.xml',
        'word/endnotes.xml',
        'word/footer*.xml',
        'word/footnotes.xml',
        'word/header*.xml',
    ];

    /**
     * Temporary directory to use to extract template
     * file into
     * @var string
     */
    protected string $tempDir;

    /**
     * The directory to use to extract template into
     * @var string
     */
    protected string $templateExtractDir;

    /**
     * Create new instance
     * @param Filesystem $filesystem
     * @param DocxTemplateRendererInterface|null $renderer
     * @param DocxConvertorInterface|null $convertor
     */
    public function __construct(
        Filesystem $filesystem,
        ?DocxTemplateRendererInterface $renderer = null,
        ?DocxConvertorInterface $convertor = null
    ) {
        $this->filesystem = $filesystem;
        $this->convertor = $convertor ?? new NullConvertor();
        $this->renderer = $renderer ?? new NullRenderer();
        $this->tempDir = sys_get_temp_dir();
    }

    /**
     * Return the convertor
     * @return DocxConvertorInterface
     */
    public function getConvertor(): DocxConvertorInterface
    {
        return $this->convertor;
    }

    /**
     * Set the convertor
     * @param DocxConvertorInterface $convertor
     * @return $this
     */
    public function setConvertor(DocxConvertorInterface $convertor): self
    {
        $this->convertor = $convertor;
        return $this;
    }

    /**
     * Return the renderer
     * @return DocxTemplateRendererInterface
     */
    public function getRenderer(): DocxTemplateRendererInterface
    {
        return $this->renderer;
    }

    /**
     * Set the renderer
     * @param DocxTemplateRendererInterface $renderer
     * @return $this
     */
    public function setRenderer(DocxTemplateRendererInterface $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Return the output template file after processed
     * @return string
     */
    public function getOutputTemplateFile(): string
    {
        return $this->outputTemplateFile;
    }

    /**
     * Return the conversion file path or content
     * @return string
     */
    public function getConvertionFile(): string
    {
        return $this->convertionFile;
    }

    /**
     * Return the data
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the data
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set template file
     * @param string $templateFile
     * @return $this
     */
    public function setTemplateFile(string $templateFile): self
    {
        $this->templateFile = $templateFile;
        return $this;
    }

    /**
     * Set the template directory
     * @param string $tempDir
     * @return $this
     */
    public function setTempDir(string $tempDir): self
    {
        $this->tempDir = $tempDir;
        return $this;
    }


    /**
     * Process the document template
     * @return void
     */
    public function process(): void
    {
        $this->copyTemplateFileToTempDir();
        $this->extractDocumentFiles();
        foreach ($this->docxFileList as $file) {
            $this->renderTemplate($file);
        }

        if (empty($this->outputTemplateFile)) {
            $this->outputTemplateFile = $this->tempDir . '/output_' . basename($this->templateFile);
        }

        $zip = new ZipArchive();
        $create = $zip->open(
            $this->outputTemplateFile,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );
        if ($create === true) {
             // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->templateExtractDir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                $name = substr($name, strlen($this->templateExtractDir . '/'));
                $zip->addFile($file->getRealPath(), $name);
            }
            $zip->close();
        } else {
            throw new DocxTemplateException(sprintf(
                'Can not open file [%s]',
                $this->outputTemplateFile
            ));
        }
    }

    /**
     * Convert the output template to another format like PDF, HTML
     * @return string
     */
    public function convert(): string
    {
        return $this->convertor->convert($this->outputTemplateFile);
    }

    /**
     * Copy the template file to temporary directory
     * @return void
     */
    protected function copyTemplateFileToTempDir(): void
    {
        $templateFile = $this->filesystem->file($this->templateFile);
        $this->templateTempFile = $templateFile->copyTo($this->tempDir)->getPath();

        $tempDir = $this->filesystem->directory($this->tempDir);
        $extractDir = $tempDir->create('tmp_extract_' . uniqid());

        $this->templateExtractDir = $extractDir->getPath();
    }

    /**
     * Extract template document file
     * @return void
     */
    protected function extractDocumentFiles(): void
    {
        $fileList = [];
        $zip = new ZipArchive();
        $open = $zip->open($this->templateTempFile);
        if ($open === true) {
            if (!$zip->extractTo($this->templateExtractDir)) {
                throw new DocxTemplateException(sprintf(
                    'Can not extract file [%s]',
                    $this->templateTempFile
                ));
            }
            $total = $zip->numFiles;
            for ($i = 0; $i < $total; $i++) {
                $stat = $zip->statIndex($i);
                if($stat !== false ){
                    foreach ($this->fileListToProcess as $fileToProcess) {
                        if (fnmatch($fileToProcess, $stat['name'])) {
                            $fileList[] = $stat['name'];
                        }
                    }
                }
            }
            $zip->close();
            $this->docxFileList = $fileList;
        } else {
            throw new DocxTemplateException(sprintf(
                'Can not open file [%s]',
                $this->templateTempFile
            ));
        }
    }

    /**
     * Render the template (replace variables)
     * @param string $file
     * @return void
     */
    protected function renderTemplate(string $file): void
    {
        $templateFile = $this->filesystem->file($this->templateExtractDir . '/' . $file);
        if ($templateFile->exists()) {
            $content = $templateFile->read();
            $renderContent = $this->renderer->render($content, $this->data);
            $templateFile->write($renderContent);
        }
    }
}
