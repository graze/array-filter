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

    public function testSingleChildConstructorEqualsMatchesOne()
    {
        $filter = new OneOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testSingleChildAddedLaterEqualsMatchesOne()
    {
        $filter = new OneOfFilter();
        $filter->addFilter(new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        }));
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testMultipleChildrenWithDifferentPropertiesMatchesOne()
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

    public function testMultipleChildrenWithTheSamePropertyMatchesOne()
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

    public function testCallableFiltersMatchesOne()
    {
        $filter = new OneOfFilter([
            function (array $data) {
                return isset($data['test']) && $data['test'] == 'value';
            }
        ]);
        static::assertTrue($filter(['test' => 'value']));
        static::assertFalse($filter(['test' => 'values']));
        static::assertFalse($filter(['tests' => 'values']));
    }

    public function testOneOfFilterCanBeInvoked()
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
