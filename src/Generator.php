<?php

/*
 * This file is part of the faker generator package.
 *
 * (c) Rafael Calleja <rafaelcalleja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FakeGenerator;

class Generator
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $sufix;

    private $value;

    private $faker;

    private $pattern;

    public function __construct($prefix, $length, $sufix = null, $pattern = '[A-Za-z0-9._%+-]')
    {
        $this->faker = new \Faker\Generator();
        $this->faker->addProvider(new \Faker\Provider\Base($this->faker));

        $this->setPrefix($prefix);
        $this->setLength($length);
        $this->setSufix($sufix);
        $this->setPattern($pattern);

        $this->buildValue();
    }

    /**
     * @return string
     */
    public function prefix()
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function length()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function sufix()
    {
        return $this->sufix;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @param $value
     */
    private function setPrefix($value)
    {
        $this->prefix = $value;
    }

    /**
     * @param $value
     */
    private function setSufix($value)
    {
        $this->sufix = $value;
    }

    /**
     * @param $value
     */
    private function setLength($value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(sprintf('legnth must be >= 1 current (%s)', $value));
        }

        $this->length = $value;
    }

    /**
     * @param $value
     */
    private function setPattern($value)
    {
        $this->pattern = $value;
    }

    private function buildValue()
    {
        $this->value = sprintf('%s%s%s', $this->prefix(), $this->faker->regexify($this->pattern.'{'.$this->length().'}'), $this->sufix());

        if ($this->value == $this->prefix()) {
            throw new \InvalidArgumentException(sprintf('legnth must be >= 1 current (%s)', $this->length()));
        }
    }
}
