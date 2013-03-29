<?php

namespace SecurityMultiTool\Http;

use SecurityMultiTool\Http\HttpsDetector;
use SecurityMultiTool\Http\HostDetector;
use SecurityMultiTool\Http\Header;
use SecurityMultiTool\Exception;
use Zend\Uri\Uri;

class Redirector
{

    protected $whitelist = array();

    protected $allowProxy = false;

    public function __construct($allowProxy = false)
    {
        $this->allowProxy = (bool) $allowProxy;
    }

    public function getRedirect($urlString, $stayLocal = true, $preserveHttps = true)
    {
        /**
         * Check that the URL has the correct format expected of a valid HTTP
         * or HTTPS URL. If so, normalize the URL.
         */
        $valid = false;
        $url = new Uri;
        try {
            $url->parse($urlString);
            if ($url->isValid() && $url->isAbsolute()) {
                $url->normalize();
                $valid = true;
            }
        } catch (\Exception $e) {
        }
        if (false === $valid) {
            throw new Exception\InvalidArgumentException(
                "Given value was not a valid absolute HTTP(S) URL: " . $url
            );
        }
        /**
         * Make sure we don't redirect from HTTPS to HTTP unless flagged by
         * the user. Using a Strict-Transport-Security header helps too!
         */
        if (true === (bool) $preserveHttps && HttpsDetector::isHttpsRequest()) {
            if (!$this->isHttps($url)) {
                throw new Exception\InvalidArgumentException(
                    "Given value was not a HTTPS URL as expected: " . $url
                );
            }
        }
        /**
         * Check if the URL meets the local host restriction unless disabled
         */
        if (true === $stayLocal && !$this->isLocal($url)) {
            throw new Exception\InvalidArgumentException(
                "Given value was not a local HTTP(S) URL: " . $url
            );
        }
        /**
         * Check if the URL host exists on a whitelist of allowed hosts
         */
        $whitelist = $this->getWhitelist();
        if (!empty($whitelist) && !$this->isWhitelisted($url)) {
            throw new Exception\InvalidArgumentException(
                "Given value was not a whitelisted URL as expected: " . $url
            );
        }
        /**
         * Get URL string after URL encoding checks and return a Location header
         * object.
         */
        $header = new Header\Location(array(
            'url' => $url->toString(),
            'status_code' => 302
        ));
        return $header;
    }

    public function redirect($url, $stayLocal = true, $preserveHttps = true, $replace = false)
    {
        $header = $this->getRedirect($url, $stayLocal, $preserveHttps);
        $header->send($replace);
    }

    public function addWhitelist(array $whitelist)
    {
        foreach ($whitelist as $value) {
            $this->addWhitelistedHost($value);
        }
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    public function addWhitelistedHost($host)
    {
        $this->whitelist[] = $host;
    }

    protected function isLocal($url)
    {
        $host = HostDetector::getLocalHost($this->allowProxy);
        $urlHost = $url->getHost();
        if ($url->getPort()) {
            $urlHost .= ':' . $url->getPort();
        }
        if ($host !== $urlHost) {
            return false;
        }
        return true;
    }

    protected function isHttps($url)
    {
        if ($url->getScheme() !== 'https') {
            return false;
        }
        return true;
    }

    protected function isWhitelisted($url)
    {
        $whitelist = $this->getWhitelist();
        if (!empty($whitelist)) {
            $host = $url->getHost();
            foreach($whitelist as $allowed) {
                if ($host === $allowed) {
                    return true;
                }
            }
        }
        return false;
    }

}