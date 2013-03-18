<?php

namespace SecurityMultiTool\BBCode;

use Decoda\Decoda;
use Decoda\Filter;
use Decoda\Hook;
use SecurityMultiTool\Html\Sanitizer;
use SecurityMultiTool\Exception;
use SecurityMultiTool\Common;

class Parser implements Common\OptionsInterface
{

    protected $parser = null;

    protected $sanitizer = null;

    protected $filter = '';

    public function __construct($cachePath, array $options = null)
    {
        $this->sanitizer = new Sanitizer($cachePath, $options);
        //$this->sanitizer->setOption('HTML.Allowed', $this->filter);
        $this->parser = new Decoda;
    }

    public function parse($bbcode, $filter = null)
    {
        $this->parser->reset($bbcode);
        $unsanitized = $this->parser->parse();
        $sanitized = $this->sanitizer->sanitize($unsanitized, $filter);
        return $sanitized;
    }

    public function addFilter(Filter $filter)
    {
        $this->parser->addFilter($filter);
    }

    public function addHook(Hook $hook)
    {
        $this->parser->addHook($hook);
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

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    public function getOptions()
    {
        return $this->sanitizer->getOptions();
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

    public function setSanitizer(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getSanitizer()
    {
        return $this->sanitizer;
    }

    public function getParser()
    {
        return $this->parser;
    }

}