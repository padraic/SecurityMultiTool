<?php

namespace SecurityMultiTool\Http\Header;

interface HeaderInterface
{

    public function getHeader();

    public function send($replace = false);

    public function setOptions(array $options);

    public function setOption($key, $value);

    public function getOption($key);

}