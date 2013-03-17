<?php

namespace SecurityMultiTool\Random;

use SecurityMultiTool\Exception;
use SecurityLib\Strength;
use RandomLib;

class Generator
{

    protected $generator = null;

    public function getBytes($length, $strong = false)
    {
        $bytes = '';
        if (function_exists('openssl_random_pseudo_bytes')
            && (version_compare(PHP_VERSION, '5.3.4') >= 0
            || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        ) {
            $bytes = openssl_random_pseudo_bytes($length, $usable);
            if (true === $usable) {
                return $bytes;
            }
        }
        if (function_exists('mcrypt_create_iv')
            && (version_compare(PHP_VERSION, '5.3.7') >= 0
            || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        ) {
            $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            if ($bytes !== false && strlen($bytes) === $length) {
                return $bytes;
            }
        }
        $checkAlternatives = (file_exists('/dev/urandom') && is_readable('/dev/urandom'))
            || class_exists('\\COM', false);
        if (true === $strong && false === $checkAlternatives) {
            throw new Exception\RuntimeException (
                'Unable to generate sufficiently strong random bytes due to a lack ',
                'of sources with sufficient entropy'
            );
        }
        $generator = $this->getAlternativeGenerator();
        return $generator->generate($length);
    }

    public function getAlternativeGenerator()
    {
        if (isset($this->generator)) {
            return $this->generator;
        }
        $factory = new RandomLib\Factory;
        $factory->registerSource(
            'HashTiming',
            '\SecurityMultiTool\Random\Source\HashTiming'
        );
        $this->generator = $factory->getMediumStrengthGenerator();
        return $this->generator;
    }

    public function getBoolean($strong = false)
    {
        $byte = $this->getBytes(1, $strong);
        return (bool) (ord($byte) % 2);
    }

    public function getInteger($min, $max, $strong = false)
    {
        if ($min > $max) {
            throw new Exception\DomainException(
                'The min parameter must be lower than max parameter'
            );
        }
        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new Exception\DomainException(
                'The supplied range between min and max must result in an integer '
                . 'no greater than the value of PHP_INT_MAX'
            );
        }
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(self::getBytes($bytes, $strong)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return ($min + $rnd);
    }

    public function getFloat($strong = false)
    {
        $bytes = static::getBytes(7, $strong);
        $bytes[6] = $bytes[6] | chr(0xF0);
        $bytes .= chr(63); // exponent bias (1023)
        list(, $float) = unpack('d', $bytes);
        return ($float - 1);
    }

    public function getString($length, $charlist = '', $strong = false)
    {
        if ($length < 1) {
            throw new Exception\InvalidArgumentException(
                'String length must be greater than zero'
            );
        }
        if (empty($charlist)) {
            $numBytes = ceil($length * 0.75);
            $bytes = $this->getBytes($numBytes, $strong);
            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        }
        $listLen = strlen($charlist);
        if ($listLen == 1) {
            return str_repeat($charlist, $length);
        }
        $bytes = $this->getBytes($length, $strong);
        $pos = 0;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = ($pos + ord($bytes[$i])) % $listLen;
            $result .= $charlist[$pos];
        }
        return $result;
    }

}