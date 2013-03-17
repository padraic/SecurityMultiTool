<?php

namespace SecurityMultiTool\Common;

abstract class AbstractOptions
{

    protected $options = array();

    public function __construct(array $options = null)
    {
        if (!is_null($options)) {
            $this->setOptions($options);
        }
    }

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public abstract function setOption($key, $value);

    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
    }

}