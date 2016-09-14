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

use SecurityMultiTool\Http\Headers;
use SecurityMultiTool\Http\Header;
use Mockery as M;

class HeadersTest extends \PHPUnit_Framework_TestCase
{

    public function testOptionSetting()
    {
        $headers = new Headers(
            array(
                'sts' => array('max_age'=>100),
                'strict_transport_security' => array('include_subdomains'=>true),
                'csrf' => array('token'=>'foo'),
                'csrf_token' => array('token'=>'bar')
            )
        );
        $expected1 = array('max_age'=>100,'include_subdomains'=>true);
        $expected2 = array('token'=>'bar');
        $expected3 = array(
            'csrf_token' => array('token'=>'bar'),
            'strict_transport_security' => array('max_age'=>100,'include_subdomains'=>true),
        );
        $this->assertSame($expected1, $headers->getOption('sts'));
        $this->assertSame($expected2, $headers->getOption('csrf'));
        $this->assertSame($expected3, $headers->getOptions());
    }

    public function testHeadersAreSent()
    {
        $h1 = M::mock('SecurityMultiTool\Http\Header\HeaderInterface');
        $h2 = M::mock('SecurityMultiTool\Http\Header\HeaderInterface');
        $h1->shouldReceive('send')->once()->with(false);
        $h2->shouldReceive('send')->once()->with(false);
        $headers = new Headers;
        //$headers->addHeader($h1)->addHeader($h2);             Daisychaining won't work because the name is the same on both headers.
        $headers->addHeader($h1);
        $headers->send();
        $headers->addHeader($h2);
        $headers->send();
    }

}