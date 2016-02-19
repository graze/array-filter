<?php

namespace Graze\ArrayFilter\Test\Unit;

use Graze\ArrayFilter\ClosureFilter;
use Graze\ArrayFilter\OneOfFilter;
use Graze\ArrayFilter\Test\TestCase;

class OneOfFilterTest extends TestCase
{
    public function testInstanceOf()
    {
        $filter = new OneOfFilter();
        static::assertInstanceOf('Graze\ArrayFilter\ArrayFilterInterface', $filter);
    }

    public function testSingleChildConstructorEquals()
    {
        $filter = new OneOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testSingleChildAddedLaterEquals()
    {
        $filter = new OneOfFilter();
        $filter->addFilter(new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        }));
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testMultipleChildrenWithDifferentProperties()
    {
        $filter = new OneOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
            new ClosureFilter('test2', function ($actual) {
                return $actual == 'value2';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertTrue($filter->matches(['test2' => 'value2']));
        static::assertTrue($filter->matches(['test' => 'values', 'test2' => 'value2']));
        static::assertFalse($filter->matches(['test' => 'values', 'test2' => 'value3']));
    }

    public function testMultipleChildrenWithTheSameProperty()
    {
        $filter = new OneOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'foo';
            }),
            new ClosureFilter('test', function ($actual) {
                return $actual == 'bar';
            }),
        ]);
        static::assertFalse($filter->matches(['test' => 'value']));
        static::assertTrue($filter->matches(['test' => 'foo']));
        static::assertTrue($filter->matches(['test' => 'bar']));
    }

    public function testFilterCanBeInvoked()
    {
        $filter = new OneOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
        ]);
        static::assertTrue(call_user_func($filter, ['test' => 'value']));
        static::assertFalse(call_user_func($filter, ['test' => 'values']));
    }
}
