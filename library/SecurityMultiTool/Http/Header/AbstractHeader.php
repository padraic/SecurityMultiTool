<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Exception;

abstract class AbstractHeader
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

    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
    }

}