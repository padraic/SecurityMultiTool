<?php

namespace SecurityMultiTool\Html;

use SecurityMultiTool\Exception;
use SecurityMultiTool\Common

class Sanitizer extends Common\AbstractOptions implements Common\OptionsInterface
{

    protected $htmlpurifier = null;

    protected $filter = '';

    public function filter($html, $filter = '')
    {

    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'value':
                # code...
                break;
            
            default:
                throw new Exception\InvalidArgumentException(
                    'Attempted to set invalid option: ' . $key
                );
                break;
        }
    }

}