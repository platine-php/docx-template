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

use Platine\DocxTemplate\Convertor\NullConvertor;
use Platine\DocxTemplate\Renderer\NullRenderer;

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

    public function __construct(
        ?DocxTemplateRendererInterface $renderer = null,
        ?DocxConvertorInterface $convertor = null
    ) {
        $this->convertor = $convertor ?? new NullConvertor();
        $this->renderer = $renderer ?? new NullRenderer();
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
}
