<?php

namespace SecurityMultiTool\Http;

use SecurityMultiTool\Http\Header;
use SecurityMultiTool\Common;
use SecurityMultiTool\Exception;

class Headers implements \Countable, Common\OptionsInterface
{

    protected $options = array();

    protected $headers = array();

    public function __construct(array $options = null)
    {
        if (!is_null($options)) {
            $this->setOptions($options);
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

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    public function setOption($key, $options)
    {
        switch ($key) {
            case 'strict_transport_security':
            case 'sts':
                try {
                    if (!isset($this->headers['strict_transport_security'])) {
                        $this->headers['strict_transport_security']
                            = new Header\StrictTransportSecurity($options);
                    } else {
                        foreach ($options as $key => $value) {
                            $this->headers['strict_transport_security']
                                ->setOption($key, $value);
                        }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
                break;

            case 'csrf_token':
            case 'csrf':
                try {
                    if (!isset($this->headers['csrf_token'])) {
                        $this->headers['csrf_token']
                            = new Header\CsrfToken($options);
                    } else {
                        foreach ($options as $key => $value) {
                            $this->headers['csrf_token']
                                ->setOption($key, $value);
                        }
                    }
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
        switch ($key) {
            case 'strict_transport_security':
            case 'sts':
                return $this->headers['strict_transport_security']->getOptions();
                break;

            case 'csrf_token':
            case 'csrf':
                return $this->headers['csrf_token']->getOptions();
                break;
            
            default:
                return null;
                break;
        }
    }

    public function getOptions()
    {
        $return = array();
        ksort($this->headers, \SORT_STRING);
        foreach ($this->headers as $key => $value) {
            $return[$key] = $value->getOptions();
        }
        return $return;
    }

    public function addHeader(Header\HeaderInterface $header)
    {
        $class = get_class($header);
        $parts = explode('\\', $class);
        $name = strtolower(array_shift($parts));
        $this->headers[$name] = $header;
        return $this;
    }

}