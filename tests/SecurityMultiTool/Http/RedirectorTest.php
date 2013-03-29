<?php
/**
 * SecurityMultiTool
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/padraic/SecurityMultiTool/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   SecurityMultiTool
 * @package    SecurityMultiTool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2013 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/SecurityMultiTool/blob/master/LICENSE New BSD License
 */

use SecurityMultiTool\Http\Redirector;

class RedirectorTest extends \PHPUnit_Framework_TestCase
{

    protected $httpHost = null;

    public function setup()
    {
        $this->redirector = new Redirector;
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->httpHost = $_SERVER['HTTP_HOST'];
        }
        $_SERVER['HTTP_HOST'] = 'www.example.com';
    }

    public function teardown()
    {
        $_SERVER['HTTP_HOST'] = $this->httpHost;
    }

    /**
    * @runInSeparateProcess
    */
    public function testSendsRedirectLocationHeader()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Requires ext/xdebug to be installed.');
        }
        $this->redirector->redirect('http://www.example.com');
        $this->assertContains('Location: http://www.example.com/', xdebug_get_headers());
        header_remove();
    }

}