<?php

namespace SecurityMultiTool\Html;

use SecurityMultiTool\Exception;
use SecurityMultiTool\Common;

class Sanitizer extends Common\AbstractOptions implements Common\OptionsInterface
{

    protected $purifier = null;

    protected $config = null;

    protected $filter = '';

    public function __construct($cachePath, array $options = null)
    {
        if ((!isset($cachePath) || !is_dir($cachePath) || !is_writable($cachePath))
        && false !== $cachePath) {
            throw new Exception\RuntimeException(
                'The HTMLPurifier HTML Sanitiser requires a cache location to '
                . 'improve performance. Please set a cache path or set the '
                . 'first parameter to the constructor of this class to false. '
                . 'Ensure the given location, if set, is writable by PHP'
            );
        }
        $this->config = \HTMLPurifier_Config::createDefault();
        if (false === $cachePath) {
            $this->getConfig()->set('Cache.DefinitionImpl', null);
        } else {
            $this->getConfig()->set('Cache.SerializerPath', rtrim($cachePath, '\\/ '));
        }
        parent::__construct($options);
    }

    public function sanitize($html, $filter = null)
    {
        return $this->getHtmlPurifier()->purify($html, $filter);
    }

    public function reset()
    {
        $this->purifier = null;
    }

    public function setFilterDefinition($filter)
    {
        $this->filter = $filter;
        $this->setOption('HTML.Allowed', $this->filter);
    }

    public function getFilterDefinition()
    {
        return $this->filter;
    }

    public function setOption($key, $value)
    {
        $this->reset();
        $this->getConfig()->set($key, $value);
    }

    public function getOption($key)
    {
        return $this->getConfig()->get($key);
    }

    public function getOptions()
    {
        throw new Exception\RuntimeException(
            'Unfortunately, there\'s no way to retrieve all options from '
            . 'HTMLPurifier_Config\'s property list object'
        );
    }

    public function setConfig(\HTMLPurifier_Config $config)
    {
        $this->reset();
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setHtmlPurifier(\HtmlPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    public function getHtmlPurifier()
    {
        if (!isset($this->purifier)) {
            $this->setHtmlPurifier(new \HTMLPurifier($this->getConfig()));
        }
        return $this->purifier;
    }

}