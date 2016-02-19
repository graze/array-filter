<?php

namespace Graze\ArrayFilter\Test\Unit;

use Graze\ArrayFilter\ClosureFilter;
use Graze\ArrayFilter\Test\TestCase;

class ClosureFilterTest extends TestCase
{
    public function testInstanceOf()
    {
        $filter = new ClosureFilter('', function ($actual) {
            return $actual == 'value';
        });
        static::assertInstanceOf('Graze\ArrayFilter\ArrayFilterInterface', $filter);
    }

    public function testBasicEquals()
    {
        $filter = new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        });
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testReturnsFalseWhenInvalidPropertySpecified()
    {
        $filter = new ClosureFilter('invalid', function ($actual) {
            return $actual == 'value';
        });
        static::assertFalse($filter->matches(['invilad' => 'value']));
    }

    public function testFilterCanBeInvoked()
    {
        $filter = new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        });
        static::assertTrue(call_user_func($filter, ['test' => 'value']));
        static::assertFalse(call_user_func($filter, ['test' => 'values']));
    }
}
