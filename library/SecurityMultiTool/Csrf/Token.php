<?php

namespace SecurityMultiTool\Csrf;

use SecurityMultiTool\Random\Generator;
use SecurityMultiTool\Exception;

class Token extends Common\AbstractOptions implements Common\OptionsInterface
{

    protected $random = null;

    protected $name = '';

    public function __construct(array $options = null)
    {
        parent::__construct($options);
        $this->random = new Generator;
    }

    public function setOption($key, $value)
    {
        switch ($key) {

            case 'name':
                $this->name = (string) $name;
                break;
            
            default:
                throw new Exception\InvalidArgumentException(
                    'Attempted to set invalid option: ' . $key
                );
                break;
        }
    }

    public function generate()
    {
        $random = $this->random->getBytes(32);
        $token = base64_encode($this->name . $random);
        return $token;
    }

}