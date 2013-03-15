<?php

namespace SecurityMultiTool\Markdown;

use dflydev\markdown\MarkdownParser;
use SecurityMultiTool\Html\Sanitizer;
use SecurityMultiTool\Exception;

class Parser
{

    protected $parser = null;

    protected $sanitizer = null;

    protected $filter = '';

    public function __construct($cachePath, array $options = null)
    {
        $this->sanitizer = new Sanitizer($cachePath, $options);
        $this->sanitizer->setOption('HTML.Allowed', $this->filter);
    }

    public function parse($markdown, $filter = null)
    {
        $unsanitized = $this->parser->transformMarkdown($markdown);
        $sanitized = $this->sanitizer->sanitize($unsanitized, $filter);
        return $sanitized;
    }

    public function resetSanitizer()
    {
        $this->sanitizer->reset();
    }

    public function setFilterDefinition($filter)
    {
        $this->filter = $filter;
        $this->resetSanitizer();
        $this->sanitizer->setOption('HTML.Allowed', $this->filter);
    }

    public function getFilterDefinition()
    {
        return $this->filter;
    }

    public function setOption($key, $value)
    {
        $this->sanitizer->setOption($key, $value);
    }

    public function getOption($key)
    {
        return $this->sanitizer->getOption($key);
    }

    public function getSanitizerConfig()
    {
        return $this->sanitizer->getConfig();
    }

}