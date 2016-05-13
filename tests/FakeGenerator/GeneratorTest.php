<?php

/*
 * This file is part of the faker generator package.
 *
 * (c) Rafael Calleja <rafaelcalleja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FakeGenerator\Tests;

use FakeGenerator\Generator;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider prefixProvider */
    public function testPrefixAndRandomPart($prefix, $length)
    {
        $generator = new Generator($prefix, $length);

        $actual = (string) $generator;

        $expected = substr($actual, 0, strlen($prefix));

        $this->assertSame($prefix, $generator->prefix());
        $this->assertSame($length, $generator->length());
        $this->assertSame($expected, $prefix);
        $this->assertSame(strlen($actual), strlen($prefix) + $length);
    }

    /** @dataProvider prefixProvider */
    public function testSufixAndRandomPart($sufix, $length)
    {
        $generator = new Generator(null, $length, $sufix);

        $actual = (string) $generator;

        $expected = substr($actual, $length);

        $this->assertSame($sufix, $generator->sufix());
        $this->assertSame($length, $generator->length());
        $this->assertSame($expected, $sufix);
        $this->assertSame(strlen($actual), strlen($sufix) + $length);
    }

    /** @dataProvider prefixProvider */
    public function testPrefixAndSufixAndRandomPart($append, $length)
    {
        $generator = new Generator($append, $length, $append);

        $actual = (string) $generator;

        $expected_prefix = substr($actual, 0, strlen($append));
        $expected_sufix = substr($actual, strlen($actual) - strlen($append));

        $this->assertSame($append, $generator->sufix());
        $this->assertSame($append, $generator->prefix());
        $this->assertSame($length, $generator->length());
        $this->assertSame($expected_prefix, $append);
        $this->assertSame($expected_sufix, $append);
        $this->assertSame(strlen($actual), strlen($append) + strlen($append) + $length);
    }

    /** @dataProvider regexProvider*/
    public function testCustomRegex($regex)
    {
        $length = 1;
        $generator = new Generator('(', $length, null, $regex);

        $actual = (string) $generator;

        $this->assertSame($length, $generator->length());

        $split = preg_split(sprintf('/%s/', $regex), $actual, -1, PREG_SPLIT_OFFSET_CAPTURE);
        $this->assertCount(2, $split);
    }

    /**
     * @dataProvider exceptionProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLenght($prefix, $length)
    {
        $generator = new Generator($prefix, $length);
    }

    public function regexProvider()
    {
        return array(
            array('[0-9]'),
            array('[0-1]'),
            array('[A-Z]'),
            array('[a-z]'),
            array('[a-b]'),
            array('[B-C]'),
            array('[-+]'),
            array('[)]'),
        );
    }

    public function prefixProvider()
    {
        return array(
            array('MON', 10),
            array('ABC', 9),
            array('DEFG', 8),
            array('HIJKL', 7),
            array('MNOPQR', 6),
            array('STUVWXY', 5),
            array('ABCDEFGH', 4),
            array('IJKLMNOPQ', 3),
            array('RSTUVWXYZA', 2),
            array('BCDEFGHIJKL', 1),
            array('MONITOR-BAR-', 5),

        );
    }

    public function exceptionProvider()
    {
        return array(
            array('MNOPQRSTUVWX', 0),
            array('YZABCDEFGHIJK', -1),
            array('123', 'a'),
            array('', 0),
        );
    }
}
