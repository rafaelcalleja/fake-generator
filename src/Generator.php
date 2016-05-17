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

    /**
     * @var int
     */
    protected $max;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var \FormalTheory_RegularExpression_Lexer
     */
    private $lexer;

    public function __construct($prefix, $length, $sufix = null, $pattern = '[A-Za-z0-9._%+-]')
    {
        $this->faker = new \Faker\Generator();
        $this->faker->addProvider(new \Faker\Provider\Base($this->faker));

        $this->lexer = new \FormalTheory_RegularExpression_Lexer();

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
     * @return int
     */
    public function max()
    {
        return $this->max;
    }

    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    public function equals(Generator $object)
    {
        return $this->prefix() == $object->prefix() &&
               $this->sufix() == $object->sufix() &&
               $this->length() == $object->length() &&
               $this->pattern() == $object->pattern() &&
               $this->value == $object->value &&
               $this->max() == $object->max()
             ;
    }

    public function next()
    {
        if ($this->max() <= 1) {
            throw new \RuntimeException('no more elements');
        }

        while (true) {
            $new = new self($this->prefix(), $this->length(), $this->sufix(), $this->pattern);

            if (!$new->equals($this)) {
                return $new;
            }
        }
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
        $pattern = $this->pattern.'{'.$this->length().'}';

        $this->value = sprintf('%s%s%s',  $this->prefix(), $this->faker->regexify($pattern), $this->sufix());

        if ($this->value == $this->prefix()) {
            throw new \InvalidArgumentException(sprintf('legnth must be >= 1 current (%s)', $this->length()));
        }

        $this->setMax($pattern);
    }

    private function setMax($value)
    {
        $pattern = sprintf('^%s$', $value);
        $dfa = $this->lexer->lex($pattern)->getDFA();
        $this->max = $dfa->countSolutions();
    }
}
