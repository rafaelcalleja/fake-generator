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

class GeneratorTest extends TestCase
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
     * @dataProvider regexValidCounts
     */
    public function testGetMaxRandomRegexWhenIsFinitePattern($pattern, $length, $count)
    {
        $generator = new Generator(null, $length, null, $pattern);

        $this->assertSame($count, $generator->max());
    }

    /**
     * @dataProvider exceptionProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLenght($prefix, $length)
    {
        $generator = new Generator($prefix, $length);
    }

    public function regexValidCounts()
    {
        return array(
            array('[0-9]', 1, 10),
            array('[0-9]', 2, 100),
            array('[0-1]', 1, 2),
            array('[A-Z]', 1, 26),
            array('[a-z]', 1, 26),
            array('[a-b]', 4, 16),
            array('[B-C]', 5, 32),
            array('[-+]', 10, 1024),
            array('[)]', 1, 1),
            array('[)]', 1, 1),
            array('[a-zA-Z0-9]', 6, (26 + 26 + 10) * (26 + 26 + 10) * (26 + 26 + 10) * (26 + 26 + 10) * (26 + 26 + 10) * (26 + 26 + 10)),
            array('[A-Za-z0-9._%+-]', 3, (26 + 26 + 10 + 5) * (26 + 26 + 10 + 5) * (26 + 26 + 10 + 5)),
            array('[A-Fa-f0-9]', 3, (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10)),
            array('[A-Fa-f0-9]', 8, (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10) * (6 + 6 + 10)),
        );
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

    public function testEquality()
    {
        $expected = new Generator(null, 1, null, '[a]');
        $actual = new Generator(null, 1, null, '[a]');

        $this->assertTrue($expected->equals($actual));
        $this->assertFalse($expected->equals(new Generator(null, 1, null, '[b]')));

        $expected = new Generator(null, 1, null, '[0-1]');
        $actual = new Generator(null, 1, null, '[1-0]');
        $this->assertFalse($expected->equals($actual));

        $expected = new Generator(null, 1, null, '[ab]');
        $actual = new Generator(null, 1, null, '[ab]');

        $this->setPropertyValue($expected, 'value', 'a');
        $this->setPropertyValue($actual, 'value', 'b');

        $this->assertFalse($expected->equals($actual));

        $expected = new Generator(null, 1, null, '[ab]');
        $actual = new Generator('a', 1, null, '[b]');
        $this->setPropertyValue($expected, 'value', 'ab');

        $this->assertSame((string) $expected, (string) $actual);
        $this->assertFalse($expected->equals($actual));
    }

    public function testNextRandomValue()
    {
        $object = new Generator(null, 1, null, '[ab]');

        $next = $object->next();
        $this->assertNotSame($object, $next);
        $this->assertNotSame((string) $object, (string) $next);
        $this->assertFalse($object->equals($next));
    }

    /**
     * @dataProvider exceptionNoDifferentValuesProvider
     * @expectedException \RuntimeException
     */
    public function testNoDiferentValueExists($prefix, $length, $suffix, $pattern)
    {
        $generator = new Generator($prefix, $length, $suffix, $pattern);
        $generator->next();
    }

    public function exceptionNoDifferentValuesProvider()
    {
        return array(
            array(null, 1, null, '[a]'),
            array('a', 1, null, '[b]'),
            array('a', 1, 'c', '[c]'),
            array(null, 1, 'c', '[d]'),
        );
    }
}
