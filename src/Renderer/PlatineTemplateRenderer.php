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
 *  @file PlatineTemplateRenderer.php
 *
 *  The renderer using Platine Template class
 *
 *  @package    Platine\DocxTemplate\Renderer
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\DocxTemplate\Renderer;

use Platine\DocxTemplate\DocxTemplateRendererInterface;
use Platine\Template\Template;

/**
 * @class PlatineTemplateRenderer
 * @package Platine\DocxTemplate\Renderer
 */
class PlatineTemplateRenderer implements DocxTemplateRendererInterface
{
    /**
     * The template instance to use
     * @var Template
     */
    protected Template $template;

    /**
     * Create new instance
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $content, array $data = []): string
    {
        $fixSplitTagsContent = $this->fixSplitTemplateTags($content);
        $tableLoopHandle = $this->handleTableLoop($fixSplitTagsContent);

        return $this->template->renderString($tableLoopHandle, $data);
    }

    /**
     * Clean the document XML tags
     * @param string $content
     * @return string
     */
    protected function fixSplitTemplateTags(string $content): string
    {
       /*
        * If part of the tag is formatted differently we won't get a match.
     * Best explained with an example:
     *
     * ```xml
     * <w:r>
     *  <w:rPr/>
     *  <w:t>Hello ${tag_</w:t>
     * </w:r>
     * <w:r>
     *  <w:rPr>
     *      <w:b/>
     *      <w:bCs/>
     *  </w:rPr>
     *  <w:t>1}</w:t>
     * </w:r>
     * ```
     *
     * The above becomes, after running through this method:
     *
     * ```xml
     * <w:r>
     *  <w:rPr/>
     *  <w:t>Hello ${tag_1}</w:t>
     * </w:r>
     */
        $matches = [];
        preg_match_all('~\{(\{|%)\s*([^\}]+)\s*(\}|%)\}~U', $content, $matches);
        foreach ($matches[0] as $value) {
            $startTagsCleaned = (string) preg_replace('/<[^>]+>/', '', $value);
            $endTagsCleaned = (string) preg_replace('/<\/[^>]+>/', '', $startTagsCleaned);
            $content = str_replace($value, $endTagsCleaned, $content);
        }

        return $content;
    }

    /**
     * Handle table loop that contains the tags "{% for xx in yyyy %}" and "{% endfor %}"
     * @param string $content
     * @return string
     */
    protected function handleTableLoop(string $content): string
    {
        $matches = [];
        preg_match_all('~<w:tr(.*?)>(.*)</w:tr>~si', $content, $matches);
        foreach ($matches[0] as $value) {
            $parts = explode('</w:tr>', $value);
            foreach ($parts as $tableRow) {
                $matchesStartLoop = [];
                preg_match(
                    '~{%\s*for\s*([a-z0-9_]+)\s*in\s*([a-z0-9_]+)\s*%}~si',
                    $tableRow,
                    $matchesStartLoop
                );
                if (!empty($matchesStartLoop[0])) {
                    $startLoop = $matchesStartLoop[0];
                    $newTableRow = $startLoop . str_replace($startLoop, '', $tableRow);
                    $content = str_replace($tableRow, $newTableRow, $content);
                }
                if (strpos($tableRow, 'endfor') !== false) {
                    $content = str_replace($tableRow . '</w:tr>', '{% endfor %}', $content);
                }
            }
        }

        return $content;
    }
}
