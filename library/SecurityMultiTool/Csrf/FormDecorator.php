<?php

namespace SecurityMultiTool\Csrf;

use SecurityMultiTool\Html\Escaper;
use SecurityMultiTool\Exception;

class FormDecorator
{

    protected $provider = null;

    protected $escaper = null;

    public function __construct(Csrf\Provider $provider, $encoding = 'utf-8')
    {
        $this->provider = $provider;
        $this->escaper = new Escaper($encoding);
    }

    public function decorate($form)
    {
        preg_match_all("/<form(.*?)>(.*?)<\\/form>/is", $form, $matches);
        if (is_array($matches)) {
            foreach ($matches as $match) {
                $token = $this->escaper->escapeHtml(
                    $this->provider->getToken()
                );
                $form = str_replace(
                    $match[0],
                    '<form' . $match[1] . '>'
                        . '<input type="hidden" name="CSRFToken" value="'
                        . $token
                        . '" />'
                        . $match[2]
                        . '</form>',
                    $form
                );
            }
        } else {
            throw new Exception\RuntimeException(
                'Unable to decorate as the given argument does not appear to '.
                'contain valid HTML form markup'
            );
        }
        return $form;
    }

}