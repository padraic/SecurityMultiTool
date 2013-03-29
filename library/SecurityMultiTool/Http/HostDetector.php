<?php

namespace SecurityMultiTool\Http;

class HostDetector
{

    public static function getLocalHost($allowProxy = false)
    {
        if (true === $allowProxy) {
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])
            && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
                if (strpos($host, ',') !== false) {
                    $hosts = explode(',', $host);
                    $host = trim(array_pop($hosts));
                }
                if (!empty($host)) {
                    return $host;
                }
            }
        }
        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            if (isset($_SERVER['SERVER_PORT'])) {
                $portStr = ':' . $_SERVER['SERVER_PORT'];
                if (substr($_SERVER['HTTP_HOST'], 0 - strlen($portStr),
                strlen($portStr)) == $portStr) {
                    return substr($_SERVER['HTTP_HOST'], 0, 0-strlen($portStr));
                }
            }
            return $_SERVER['HTTP_HOST'];
        }
        if (!isset($_SERVER['SERVER_NAME']) || !isset($_SERVER['SERVER_PORT'])) {
            return '';
        }
        return $_SERVER['SERVER_NAME'];
    }

}