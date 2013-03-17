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

use SecurityMultiTool\Html\Sanitizer;
use Mockery as M;

class SanitizerTest extends \PHPUnit_Framework_TestCase
{

    protected $cache = '';

    protected $sanitizer = null;

    public function setup()
    {
        $this->cache = sys_get_temp_dir();
        $this->sanitizer = new Sanitizer($this->cache);
    }

    public function testSanitizerCreationThrowsExceptionIfCacheDirectoryNotExists()
    {
        $this->setExpectedException('\SecurityMultiTool\Exception\RuntimeException');
        $sanitizer = new Sanitizer('/does/not/exist');
    }

    public function testSanitizerCreationCreatesHtmlPurifierConfig()
    {
        $this->assertTrue($this->sanitizer->getConfig() instanceof \HtmlPurifier_Config);
    }

    public function testOptionGetterRetrievesCachePath()
    {
        $this->assertEquals($this->cache, $this->sanitizer->getOption('Cache.SerializerPath'));
    }

    public function testCacheDisabledWhenPassingFalseAsCachePath()
    {
        $sanitizer = new Sanitizer(false);
        $this->assertEquals(null, $sanitizer->getOption('Cache.DefinitionImpl'));
    }

    public function testCanResetHtmlPurifierToNewInstance()
    {
        $purifier1 = $this->sanitizer->getHtmlPurifier();
        $this->sanitizer->reset();
        $purifier2 = $this->sanitizer->getHtmlPurifier();
        $this->assertNotEquals(
            spl_object_hash($purifier1),
            spl_object_hash($purifier2)
        );
    }

    public function testSettingNewConfigWillResetHtmlPurifier()
    {
        $config = \HTMLPurifier_Config::createDefault();
        $purifier1 = $this->sanitizer->getHtmlPurifier();
        $this->sanitizer->setConfig($config);
        $purifier2 = $this->sanitizer->getHtmlPurifier();
        $this->assertNotEquals(
            spl_object_hash($purifier1),
            spl_object_hash($purifier2)
        );
    }

    public function testSettingNewOptionValueWillResetHtmlPurifier()
    {
        $purifier1 = $this->sanitizer->getHtmlPurifier();
        $this->sanitizer->setOption('Cache.DefinitionImpl', null);
        $purifier2 = $this->sanitizer->getHtmlPurifier();
        $this->assertNotEquals(
            spl_object_hash($purifier1),
            spl_object_hash($purifier2)
        );
    }

    public function testSanitizeMethodCallsHtmlPurifier()
    {
        $purifier = M::mock('HTMLPurifier');
        $purifier->shouldReceive('purify')->once()->with('html', null);
        $this->sanitizer->setHtmlPurifier($purifier);
        $this->sanitizer->sanitize('html');
    }

    public function testOptionsMapToHtmlPurifierConfigObject()
    {
        $config = M::mock('HTMLPurifier_Config');
        $config->shouldReceive('set')->once()->with('foo', 'bar');
        $config->shouldReceive('set')->once()->with('foo1', 'bar1');
        $config->shouldReceive('set')->once()->with('foo2', 'bar2');
        $config->shouldReceive('get')->once()->with('foo')->andReturn('baz');
        $config->shouldReceive('get')->once()->with('foo1')->andReturn('baz1');
        $config->shouldReceive('get')->once()->with('foo2')->andReturn('baz2');
        $this->sanitizer->setConfig($config);
        $this->sanitizer->setOption('foo', 'bar');
        $this->sanitizer->setOptions(array(
            'foo1' => 'bar1',
            'foo2' => 'bar2'
        ));
        $this->assertEquals('baz', $this->sanitizer->getOption('foo'));
        $this->assertEquals('baz1', $this->sanitizer->getOption('foo1'));
        $this->assertEquals('baz2', $this->sanitizer->getOption('foo2'));
    }

}