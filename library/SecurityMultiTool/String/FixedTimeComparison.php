<?php

namespace SecurityMultiTool\String;

class FixedTimeComparison
{

    public static function compare($string1, $string2)
    {
        $string1 = (string) $string1;
        $string2 = (string) $string2;
        if (strlen($string1) !== strlen($string2)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($string1); $i++) {
            $result |= ord($string1[$i]) ^ ord($string2[$i]);
        }
        return $result == 0;
    }

}