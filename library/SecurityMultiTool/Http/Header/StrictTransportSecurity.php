<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Exception;

class StrictTransportSecurity extends AbstractHeader implements HeaderInterface
{

    protected $options = array(
        'max_age' => 1209600,
        'include_subdomains' => false
    );

    public function getHeader()
    {
        $header = 'Strict-Transport-Security: ';
        $header .= 'max-age=' . $this->getOption('max_age');
        if (true === $this->getOption('include_subdomains')) {
            $header .= '; includeSubDomains';
        }
        return $header;
    }

    public function send($replace = false)
    {
        if ($this->isHttpsRequest()) {
            header($this->getHeader(), $replace);
        } else {
            $location = 'Location: https://'
                . $_SERVER['HTTP_HOST']
                . $_SERVER['REQUEST_URI'];
            header($location, true, 301);
            exit(0);
        }
    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'max_age':
                $this->options['max_age'] = (int) $value;
                break;

            case 'include_subdomains':
                $this->options['include_subdomains'] = (bool) $value;
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