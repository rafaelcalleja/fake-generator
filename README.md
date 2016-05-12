# PHP Fake Generator

#[![Build Status](https://travis-ci.org/rafaelcalleja/fake-generator.svg?branch=master)](https://travis-ci.org/rafaelcalleja/fake-generator)

## Usage

```php
use FakeGenerator\Generator;

$generator = new Generator('PREFIX', 10);

$value = (string) $generator;

echo $value;

//print PREFIXAbvfD2$ssd
```
