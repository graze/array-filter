<?php

namespace Graze\ArrayFilter\Test\Integration;

use Graze\ArrayFilter\AllOfFilter;
use Graze\ArrayFilter\ClosureFilter;
use Graze\ArrayFilter\Test\TestCase;
use Respect\Validation\Validator as v;

class RespectValidationTest extends TestCase
{
    public function testClosureFilterWithValidator()
    {
        $filter = new ClosureFilter('test', v::stringType());
        static::assertTrue($filter->matches(['test' => 'is a string']));
        static::assertFalse($filter->matches(['test' => 2.4]));
    }

    public function testRespectValidatorsAreCallable()
    {
        $filter = v::key('cake', v::stringType());
        static::assertTrue($filter(['cake' => 'a string']));
        static::assertTrue($filter(['cake' => 'a string', 'other']));
        static::assertFalse($filter(['cake' => 12]));
        static::assertFalse($filter([]));
        static::assertFalse($filter(['other']));
    }

    public function testValidatorWithFilterGroups()
    {
        $allOfFilter = new AllOfFilter([
            new ClosureFilter('name', v::intVal()),
            v::key('key', v::regex('/test.+/i')),
        ]);

        static::assertTrue($allOfFilter->matches(['name' => '1234', 'key' => 'test47382']));
        static::assertFalse($allOfFilter->matches(['name' => 'test', 'key' => 'test47382']));
        static::assertFalse($allOfFilter->matches(['name' => '1234', 'key' => 'test']));
    }
}
