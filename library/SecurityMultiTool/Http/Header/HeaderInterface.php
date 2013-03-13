<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Common;

interface HeaderInterface extends Common\OptionsInterface
{

    public function getHeader();

    public function send($replace = false);

    public function __toString();

}