<?php

namespace SecurityMultiTool\Http;

use SecurityMultiTool\Http\Header;

class Headers implements \Countable
{

    protected $options = array();

    protected $headers = array();

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

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'strict_transport_security':
            case 'sts':
                try {
                    $this->headers['strict_transport_security']
                        = new Header\StrictTransportSecurity($value);
                } catch (\Exception $e) {
                    throw $e;
                }
                break;
            
            default:
                throw new Exception\InvalidArgumentException(
                    'Header type not recognised in options: ' . $key
                );
                break;
        }
    }

    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
    }

    public function send($replace = false)
    {
        ksort($this->headers, \SORT_STRING);
        foreach ($this->headers as $value) {
            $value->send($replace);
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function toArray()
    {
        $headers = array();
        foreach ($this->headers as $value) {
            $headers = $value->getHeader();
        }
        asort($headers, \SORT_STRING);
        return $headers;
    }

    public function toString()
    {
        $string = '';
        $headers = $this->toArray();
        foreach ($headers as $header) {
            $string .= sprintf('%s\r\n', $header);
        }
        return $string;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function count()
    {
        return count($this->headers);
    }

}