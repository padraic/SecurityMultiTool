<?php

namespace SecurityMultiTool\Http;

class HttpsDetector
{

    public static function isHttpsRequest()
    {
        $https = null;
        if (isset($_SERVER['HTTPS'])) {
            $https = strtolower($_SERVER['HTTPS']); 
            if ($https == 'on' || $https == '1') {
                return true;
            }
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $https = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']); 
            if ($https == 'https') {
                return true;
            }
        }
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            return true;
        }
        return false;
    }

}