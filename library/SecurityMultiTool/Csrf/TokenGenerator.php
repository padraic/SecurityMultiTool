<?php

namespace SecurityMultiTool\Csrf;

use SecurityMultiTool\Random\Generator;

class TokenGenerator
{

    protected $random = null;

    public function __construct()
    {
        $this->random = new Generator;
    }

    public function generate()
    {
        $random = $this->random->getBytes(32);
        $token = base64_encode($random);
        return $token;
    }

}