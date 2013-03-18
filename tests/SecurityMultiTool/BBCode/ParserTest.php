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

use SecurityMultiTool\BBCode\Parser;
use Mockery as M;

class BBCodeParserTest extends \PHPUnit_Framework_TestCase
{

    public $cache = '';
    public $parser = null;

    public function setup()
    {
        $this->cache = sys_get_temp_dir();
        $this->parser = new Parser($this->cache);
    }

    public function testParserCreationThrowsExceptionIfCacheDirectoryNotExists()
    {
        $this->setExpectedException('\SecurityMultiTool\Exception\RuntimeException');
        $parser = new Parser('/does/not/exist');
    }

    public function testParserCreationCreatesHtmlPurifierConfig()
    {
        $this->assertTrue($this->parser->getSanitizerConfig() instanceof \HtmlPurifier_Config);
    }

    public function testOptionGetterRetrievesCachePath()
    {
        $this->assertEquals($this->cache, $this->parser->getOption('Cache.SerializerPath'));
    }

    public function testCanDisableSanitizerCache()
    {
        $parser = new Parser(false);
        $this->assertEquals(null, $parser->getOption('Cache.DefinitionImpl'));
    }

    public function testFilterDefinitionAndSettingResetsHtmlPurifierInstance()
    {
        $this->assertEquals('', $this->parser->getFilterDefinition());
        $h1 = $this->parser->getSanitizer()->getHtmlPurifier();
        $this->parser->setFilterDefinition('p,em');
        $this->assertEquals('p,em', $this->parser->getFilterDefinition());
        $this->assertEquals('p,em', $this->parser->getOption('HTML.Allowed'));
        $this->assertNotEquals(
            spl_object_hash($h1),
            spl_object_hash($this->parser->getSanitizer()->getHtmlPurifier())
        );
    }

    public function testParsingCallsHtmlPurifier()
    {
        $sanitizer = M::mock('SecurityMultiTool\Html\Sanitizer');
        $sanitizer->shouldReceive('sanitize')->once()->with("foo", null);
        $this->parser->setSanitizer($sanitizer);
        $this->parser->parse('foo');
    }

    public function testCanRestSanitizer()
    {
        $sanitizer = M::mock('SecurityMultiTool\Html\Sanitizer');
        $sanitizer->shouldReceive('reset')->once();
        $this->parser->setSanitizer($sanitizer);
        $this->parser->resetSanitizer();
    }

}