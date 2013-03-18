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

use SecurityMultiTool\Http\Header\StrictTransportSecurity;

class StrictTransportSecurityTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsOptionsInterfaceAndAbstractClass()
    {
        $this->assertTrue(is_subclass_of(
            'SecurityMultiTool\Http\Header\StrictTransportSecurity',
            'SecurityMultiTool\Common\AbstractOptions'
        ));
        $this->assertTrue(is_subclass_of(
            'SecurityMultiTool\Http\Header\StrictTransportSecurity',
            'SecurityMultiTool\Common\OptionsInterface'
        ));
    }

    public function testHeaderConstructionWithMaxAge()
    {
        $header = new StrictTransportSecurity(array('max_age'=>'10000'));
        $this->assertEquals(
            "Strict-Transport-Security: max-age=10000",
            $header->getHeader()
        );
    }

    public function testHeaderConstructionWithIncludeSubdomains()
    {
        $header = new StrictTransportSecurity(array('include_subdomains'=>true));
        $this->assertEquals(
            "Strict-Transport-Security: max-age=1209600; includeSubDomains",
            $header->getHeader()
        );
    }

    public function testThrowsExceptionOnInvalidOptionName()
    {
        $this->setExpectedException('SecurityMultiTool\Exception\InvalidArgumentException');
        $header = new StrictTransportSecurity(array('foo'=>'bar'));
    }

    /**
    * @runInSeparateProcess
    */
    public function testHeaderIsSent()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Requires ext/xdebug to be installed.');
        }
        $https = null;
        if (isset($_SERVER['HTTPS'])) {
            $https = $_SERVER['HTTPS'];
        }
        $_SERVER['HTTPS'] = 'on';
        $header = new StrictTransportSecurity();
        $header->send();
        $_SERVER['HTTPS'] = $https;
        $this->assertContains('Strict-Transport-Security: max-age=1209600', xdebug_get_headers());
        header_remove();
    }

}