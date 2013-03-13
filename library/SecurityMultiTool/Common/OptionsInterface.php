<?php

namespace SecurityMultiTool\Common;

interface OptionsInterface
{

    public function setOptions(array $options);

    public function setOption($key, $value);

    public function getOption($key);
    
}