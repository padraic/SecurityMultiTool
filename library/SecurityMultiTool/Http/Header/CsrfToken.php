<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Exception;

class CsrfToken extends AbstractHeader implements HeaderInterface
{

    public function getHeader()
    {
        $header = 'X-CSRFToken: ' . $this->getOption('token');
        return $header;
    }

    public function send($replace = false)
    {
        header($this->getHeader(), $replace);
    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'token':
                $this->options['token'] = (string) $value;
                break;
            
            default:
                throw new Exception\InvalidArgumentException(
                    'Attempted to set invalid option: ' . $key
                );
                break;
        }
    }

    public function __toString()
    {
        return $this->getHeader();
    }

}