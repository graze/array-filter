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

    public function testClosureFilterCanBeInvoked()
    {
        $filter = new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        });
        static::assertTrue(call_user_func($filter, ['test' => 'value']));
        static::assertFalse(call_user_func($filter, ['test' => 'values']));
    }
}
