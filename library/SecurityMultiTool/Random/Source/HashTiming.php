<?php

namespace SecurityMultiTool\Random\Source;

use RandomLib;
use SecurityLib\Strength;

class HashTiming implements RandomLib\Source
{

    public static function getStrength()
    {
        return new Strength(Strength::VERYLOW);
    }

    public function generate($size)
    {
        $result = '';
        $entropy = '';
        $msec_per_round = 100;
        $bits_per_round = 2;
        $total = $size;
        $bytes = 0;
        $hash_length = 20;
        $rounds = 0;
        while (strlen($result) < $size) {
            $bytes = ($total > $hash_length)? $hash_length : $total;
            $total -= $bytes;
            for ($i=1; $i < 3; $i++) {
                $t1 = microtime(true);
                $seed = mt_rand();
                for ($j=1; $j < 50; $j++) { 
                    $seed = sha1($seed);
                }
                $t2 = microtime(true);
                $entropy .= $t1 . $t2;
            }
            $rounds = (int) ($msec_per_round * 50 / (int) (($t2 - $t1) * 1000000));
            $iter = $bytes * (int) (ceil(8 / $bits_per_round));
            for ($i = 0; $i < $iter; $i ++)
            {
                $t1 = microtime();
                $seed = sha1(mt_rand());
                for ($j = 0; $j < $rounds; $j++)
                {
                   $seed = sha1($seed);
                }
                $t2 = microtime();
                $entropy .= $t1 . $t2;
            }
            $result .= sha1($entropy, true);
        }
        return substr($result, 0, $size);
    }

}