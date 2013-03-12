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
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $usable);
            if (true === $usable) {
                return $bytes;
            }
        } elseif (function_exists('mcrypt_create_iv')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'
            || version_compare(PHP_VERSION, '5.3.7') >= 0) {
                $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                if ($bytes !== false && strlen($bytes) === $length) {
                    return $bytes;
                }
            }
        }
        $checkAlternatives = (file_exists('/dev/urandom') && is_readable('/dev/urandom'))
            || class_exists('\\COM', false);
        if (true === $strong && false === $checkAlternatives) {
            throw new Exception\RuntimeException (
                'Unable to generate sufficiently strong random bytes'
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

}