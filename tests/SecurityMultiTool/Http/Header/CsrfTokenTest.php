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

use SecurityMultiTool\Http\Header\CsrfToken;
use Mockery as M;

class CsrfTokenTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsOptionsInterfaceAndAbstractClass()
    {
        $this->assertTrue(is_subclass_of(
            'SecurityMultiTool\Http\Header\CsrfToken',
            'SecurityMultiTool\Common\AbstractOptions'
        ));
        $this->assertTrue(is_subclass_of(
            'SecurityMultiTool\Http\Header\CsrfToken',
            'SecurityMultiTool\Common\OptionsInterface'
        ));
    }

    public function testHeaderConstruction()
    {
        $header = new CsrfToken(array('token'=>'foo'));
        $this->assertEquals(
            "X-CSRFToken: foo",
            $header->getHeader()
        );
    }

    public function testThrowsExceptionOnInvalidOptionName()
    {
        $this->setExpectedException('SecurityMultiTool\Exception\InvalidArgumentException');
        $header = new CsrfToken(array('foo'=>'bar'));
    }

    /**
    * @runInSeparateProcess
    */
    public function testHeaderIsSent()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Requires ext/xdebug to be installed.');
        }
        $header = new CsrfToken(array('token'=>'foo'));
        $header->send();
        $this->assertContains('X-CSRFToken: foo', xdebug_get_headers());
        header_remove();
    }

}