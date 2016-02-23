<?php
/**
 * This file is part of graze/array-filter
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/array-filter/blob/master/LICENSE.md
 * @link    https://github.com/graze/array-filter
 */

namespace Graze\ArrayFilter\Test\Unit;

use Graze\ArrayFilter\AllOfFilter;
use Graze\ArrayFilter\ClosureFilter;
use Graze\ArrayFilter\Test\TestCase;

class AllOfFilterTest extends TestCase
{
    public function testInstanceOf()
    {
        $filter = new AllOfFilter();
        static::assertInstanceOf('Graze\ArrayFilter\ArrayFilterInterface', $filter);
    }

    public function testSingleChildConstructorEqualsMatchesAll()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testSingleChildAddedLaterEqualsMatchesAll()
    {
        $filter = new AllOfFilter();
        $filter->addFilter(new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        }));
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testMultipleChildrenWithDifferentPropertiesMatchesAll()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
            new ClosureFilter('test2', function ($actual) {
                return $actual == 'value2';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value', 'test2' => 'value2']));
        static::assertFalse($filter->matches(['test' => 'values', 'test2' => 'value2']));
    }

    public function testMultipleChildrenWithTheSamePropertyMatchesAll()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual != 'foo';
            }),
            new ClosureFilter('test', function ($actual) {
                return $actual != 'bar';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'foo']));
        static::assertFalse($filter->matches(['test' => 'bar']));
    }

    public function testAllOfFilterCanBeInvoked()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
        ]);
        static::assertTrue(call_user_func($filter, ['test' => 'value']));
        static::assertFalse(call_user_func($filter, ['test' => 'values']));
    }
}
