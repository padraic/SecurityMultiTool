<?php

namespace SecurityMultiTool\Crypt;

use SecurityMultiTool\Exception;
use SecurityLib\Strength;

class Random
{

    public function getBytes($length, $strong = false)
    {
        if ($length <= 0) {
            return false;
        }
        $opensslWeakBytes = null;
        if (extension_loaded('openssl')) {
            $rand = openssl_random_pseudo_bytes($length, $secure);
            if ($secure === true) {
                return $rand;
            } else {
                $opensslWeakBytes = $rand;
            }
        }
        if (extension_loaded('mcrypt')) {
            // PHP bug #55169
            // @see https://bugs.php.net/bug.php?id=55169
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' ||
                version_compare(PHP_VERSION, '5.3.7') >= 0) {
                $rand = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                if ($rand !== false && strlen($rand) === $length) {
                    return $rand;
                }
            }
        }
        if (file_exists('/dev/urandom') && is_readable('/dev/urandom')) {
            $file = fopen('/dev/urandom', 'rb');
            if ($file) {
                stream_set_read_buffer($file, 0);
                $result = fread($file, $length);
                fclose($file);
                return $result;
            }
        }
        /**
         * OpenBSD PRNG alt. to urandom
         */
        if (file_exists('/dev/arandom') && is_readable('/dev/arandom')) {
            $file = fopen('/dev/arandom', 'rb');
            if ($file) {
                stream_set_read_buffer($file, 0);
                $result = fread($file, $length);
                fclose($file);
                return $result;
            }
        }
        if ($strong) {
            throw new Exception\RuntimeException(
                'This PHP environment doesn\'t support secure random number generation. ' .
                'Please consider installing the OpenSSL and/or Mcrypt extensions'
            );
        }
        $altGenerator = $this->getAlternativeGenerator();
        return $altGenerator->generate($length);
    }

    public function getAlternativeGenerator()
    {
        if (!isset($this->altGenerator)) {
            $factory = new \RandomLib\Factory;
            $this->altGenerator = $factory->getGenerator(new Strength(Strength::VERYLOW));
        }
        return $this->altGenerator;
    }

}