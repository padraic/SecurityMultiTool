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

use SecurityMultiTool\String\FixedTimeComparison;
use SecurityMultiTool\Random\Generator;

class FixedTimeComparisonTest extends \PHPUnit_Framework_TestCase
{

    public function testCompareShouldReturnTrueOnMatchingStrings()
    {
        $this->assertTrue(
            FixedTimeComparison::compare('foo', 'foo')
        );
    }

    public function testCompareShouldReturnFalseOnMismatchedStrings()
    {
        $this->assertFalse(
            FixedTimeComparison::compare('foo', 'bar')
        );
    }

    public function testCompareShouldReturnTrueOnMatchingStringAndInteger()
    {
        $this->assertTrue(
            FixedTimeComparison::compare('123', 123)
        );
        $this->assertTrue(
            FixedTimeComparison::compare(123, '123')
        );
    }

    public function testCompareShouldReturnFalseOnMismatchedStringLengths()
    {
        $this->assertFalse(
            FixedTimeComparison::compare('foo', 'foobar')
        );
    }

    public function testCompareStringsWithFixedTime()
    {
        $random = new Generator;
        $string1 = $random->getBytes(512);
        $string2 = $random->getBytes(512);
        $t1 = microtime(true) * 1000000;
        $result1 = ($string1 == $string2);
        $t2 = microtime(true) * 1000000;
        $result2 = FixedTimeComparison::compare($string1, $string2);
        $t3 = microtime(true) * 1000000;
        /**
         * Timings are really difficult to test consistently but t2-t1 should
         * be about 5 microseconds and t3-t2 should be around 7000+ microseconds
         * demonstrating that the fixed time comparison is not returning results
         * prematurely on mismatched bytes.
         *
         * Here I'll just test the fixed time test takes longer by at least a
         * factor of 100 (could use 512 but a 100 is enough to prove the fixed
         * time comparison is working given we're using completely random bytes)
         */
        $this->assertTrue(($t3-$t2) > ($t2-$t1));
        $this->assertTrue(($t3-$t2) > (($t2-$t1)*100));
    }

}