[![Build Status](https://travis-ci.org/SimoneS93/Partial.svg?branch=master)](https://travis-ci.org/SimoneS93/Partial)

Apply partial arguments to your functions.

## Install

```sh
$ composer require simones/partial
```

## Usage

Bind a subset of a function's arguments and get a new function back that accepts the remaining arguments. It is most useful if you write functional programming or don't like using `use` with anonymous functions.
You can get a `Partial` instance via instantiation or with the `partial` helper.

Here are some examples (for more, head to the spec):

```php
// one-argument binding
$hello = partial('printf', ['Hello, %s']);
$hello('world') // print "Hello, world"
// or
$hello->call('world')


// multiple arguments binding
$countdown = partial('printf', ['%s, %s, %s, go!', 'Three']);
$countdown('Two', 'One'); // print "Three, Two, One, go!"


// you can skip argument, and they will be filled on call time
$countdown = partial('printf', ['%s, %s, %s, go!', Partial::SKIP, 'Two']);
$countdown('Trhee', 'One'); // print "Three, Two, One, go!"
```