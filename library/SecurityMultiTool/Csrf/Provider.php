<?php

namespace SecurityMultiTool\Csrf;

use SecurityMultiTool\String\FixedTimeComparison;
use SecurityMultiTool\Exception;

class Provider extends Common\AbstractOptions implements Common\OptionsInterface
{

    protected $generator = null;

    protected $token = '';

    protected $options = array(
        'token_name_prefix' => 'CSRFToken',
        'name' => '',
        'timeout' => 3600
    );

    public function __construct(array $options = null)
    {
        parent:__construct($options);
        $this->generator = new Csrf\TokenGenerator;
    }

    public function getToken($refresh = false)
    {
        if (!empty($this->token) && false === $refresh) {
            return $this->token;
        }
        $this->token = $this->generator->generate();
        $this->storeTokenToSession();
        return $this->token;
    }

    public function getTokenName()
    {
        return implode(
            ':',
            array(
                $this->getOption('token_name_prefix'),
                $this->getOption('name')
            )
        );
    }

    public function getName()
    {
        return $this->getOption('name');
    }

    public function getTimeout()
    {
        return $this->getOption('timeout');
    }

    public function isValid($token, $tokenName = null)
    {
        if (is_null($tokenName)) {
            $tokenName = $this->getTokenName();
        }
        try {
            $array = $this->retrieveTokenFromSession($tokenName);
        } catch (Exception\RuntimeException $e) {
            return false; //TODO: Set lastException for debug recall
        }
        if (empty($array) || !is_array($array) || !isset($array['token'])
        || !isset($array['expire'])) {
            return false;
        }
        $time = time();
        if ((int) $array['expire'] >= $time) {
            return false;
        }
        $result = FixedTimeComparison::compare($token, $array['token']);
        return $result;
    }

    protected function storeTokenToSession()
    {
        if (!session_id()) {
            session_start();
        }
        $expire = 0;
        if ($this->getTimeout() > 0) {
            $expire = time() + $this->getTimeout();
        }
        $_SESSION[$this->getTokenName()] = array(
            'token' => $this->getToken(),
            'expire' => $expire
        );
    }

    protected function retrieveTokenFromSession($tokenName)
    {
        if (!session_id()) {
            throw new Exception\RuntimeException(
                'A PHP Session has not been started so session storage is '
                . 'unavailable'
            );
        }
        if (!isset($_SESSION[$tokenName])) {
            throw new Exception\RuntimeException(
                'Session data does not include a token for the current token '
                . 'name: ' . $tokenName
            );
        }
        return $_SESSION[$tokenName];

    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'timeout':
                $this->options['timeout'] = (int) $value;
                break;

            case 'name':
                $this->options['name'] = (string) $value;
                break;

            case 'token_name_prefix':
                $this->options['token_name_prefix'] = (string) $value;
                break;
            
            default:
                throw new Exception\InvalidArgumentException(
                    'Attempted to set invalid option: ' . $key
                );
                break;
        }
    }

}