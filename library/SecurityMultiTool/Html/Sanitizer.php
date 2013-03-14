<?php

namespace SecurityMultiTool\Html;

use SecurityMultiTool\Exception;
use SecurityMultiTool\Common

class Sanitizer extends Common\AbstractOptions implements Common\OptionsInterface
{

    protected $purifier = null;

    protected $config = null;

    public function __construct($cachePath, array $options = null)
    {
        if (!isset($cachePath) || !is_dir($cachePath || !is_writable($cachePath))) {
            throw new Exception\RuntimeException(
                'The HTMLPurifier HTML Sanitiser requires a cache location to '
                . 'improve performance. Please set a cache path or set the '
                . 'first parameter to the constructor of this class to false. '
                . 'Ensure the given location, if set, is writable by PHP'
            );
        }
        $this->config = \HTMLPurifier_Config::createDefault();
        if (false === $cachePath) {
            $this->getConfig()->set('Core.DefinitionCache', null);
        } else {
            $this->getConfig()->set('Cache.SerializerPath', rtrim($cachePath, '\\/ '));
        }
        parent::__construct($options);
    }

    public function sanitize($html, $filter = '')
    {
        if (!isset($this->purifier)) {
            $this->purifier = new \HTMLPurifier($this->getConfig());
        }
        return $this->purifier->purify($html, $filter);
    }

    public function reset()
    {
        unset($this->purifier);
    }

    public function setOption($key, $value)
    {
        $this->getConfig()->set($key, $value);
    }

    public function getOption($key)
    {
        return $this->getConfig()->get($key);
    }

    public function getConfig()
    {
        return $this->config;
    }

}