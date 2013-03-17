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

use SecurityMultiTool\Random\Generator;
use SecurityMultiTool\Random\Source;
use Mockery as M;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->rand = new Generator;
    }

    public static function provideRandInt()
    {
        return array(
            array(2, 1, 10000, 100, 0.9, 1.1, false),
            array(2, 1, 10000, 100, 0.8, 1.2, true)
        );
    }

    public function testRandBytes()
    {
        for ($length = 1; $length < 4096; $length++) {
            $rand = $this->rand->getBytes($length);
            $this->assertTrue($rand !== false);
            $this->assertEquals($length, strlen($rand));
        }
    }

    public function testRandBoolean()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = $this->rand->getBoolean();
            $this->assertTrue(is_bool($rand));
        }
    }

    /**
     * A Monte Carlo test that generates $cycles numbers from 0 to $tot
     * and test if the numbers are above or below the line y=x with a
     * frequency range of [$min, $max]
     *
     * Note: this code is inspired by the random number generator test
     * included in the PHP-CryptLib project of Anthony Ferrara
     * @see https://github.com/ircmaxell/PHP-CryptLib
     *
     * @dataProvider provideRandInt
     */
    public function testRandInteger($num, $valid, $cycles, $tot, $min, $max, $strong)
    {
        try {
            $test = $this->rand->getBytes(1, $strong);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $i     = 0;
        $count = 0;
        do {
            $up   = 0;
            $down = 0;
            for ($i = 0; $i < $cycles; $i++) {
                $x = $this->rand->getInteger(0, $tot, $strong);
                $y = $this->rand->getInteger(0, $tot, $strong);
                if ($x > $y) {
                    $up++;
                } elseif ($x < $y) {
                    $down++;
                }
            }
            $this->assertGreaterThan(0, $up);
            $this->assertGreaterThan(0, $down);
            $ratio = $up / $down;
            if ($ratio > $min && $ratio < $max) {
                $count++;
            }
            $i++;
        } while ($i < $num && $count < $valid);

        if ($count < $valid) {
            $this->fail('The random number generator failed the Monte Carlo test');
        }
    }

    public function testIntegerRangeFail()
    {
        $this->setExpectedException(
            '\DomainException'
        );
        $rand = $this->rand->getInteger(100, 0);
    }

    public function testRandFloat()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = $this->rand->getFloat();
            $this->assertTrue(is_float($rand));
            $this->assertTrue(($rand >= 0 && $rand <= 1));
        }
    }

    public function testGetString()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = $this->rand->getString($length, '0123456789abcdef');
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-f]+$#', $rand) === 1);
        }
    }

    public function testGetStringBase64()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = $this->rand->getString($length);
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-zA-Z+/]+$#', $rand) === 1);
        }
    }

    public function testHashTimingSourceStrengthIsVeryLow()
    {
        $this->assertEquals(1, (string) Source\HashTiming::getStrength());
    }

    public function testHashTimingSourceStrengthIsRandomWithCorrectLength()
    {
        $source = new Source\HashTiming;
        $rand = $source->generate(32);
        $this->assertTrue(32 === strlen($rand));
        $rand2 = $source->generate(32);
        $this->assertNotEquals($rand, $rand2);
    }

    public function testAltGeneratorIsRandomWithCorrectLength()
    {
        $source = $this->rand->getAlternativeGenerator();
        $rand = $source->generate(32);
        $this->assertTrue(32 === strlen($rand));
        $rand2 = $source->generate(32);
        $this->assertNotEquals($rand, $rand2);
    }

}