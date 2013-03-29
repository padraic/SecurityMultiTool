<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Exception;

class Location extends AbstractHeader implements HeaderInterface
{

    protected $options = array(
        'url' => '',
        'status_code' => 302
    );

    public function getHeader()
    {
        $header = 'Location: ' . $this->getOption('url');
        return $header;
    }

    public function send($replace = false)
    {
        header($this->getHeader(), $replace, $this->getOption('status_code'));
    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'url':
                $this->options['url'] = $value;
                break;

            case 'status_code':
                $this->options['status_code'] = (int) $value;
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