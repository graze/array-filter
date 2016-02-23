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

namespace Graze\ArrayFilter\Test\Unit\Factory;

use DateTime;
use Graze\ArrayFilter\Factory\ValueFactory;
use Graze\ArrayFilter\Test\TestCase;

class ValueFactoryTest extends TestCase
{
    /**
     * @var ValueFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new ValueFactory();

        if ('' === ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }

    public function testADateBeingTheOnlyPropertyInAValueWillReturnADateObject()
    {
        $value = '{date:now}';
        $date = $this->factory->parseValue($value);
        $now = new DateTime();
        $result = new DateTime($date);

        static::assertInstanceOf('DateTimeInterface', $result);
        static::assertEquals($now->format('Y-m-d'), $result->format('Y-m-d'));
        static::assertRegExp('/\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}\+\d{2}\:\d{2}$/i', $date);
    }

    public function testIntComparison()
    {
        $value = '{date:now:U}';
        $time = time();
        $result = $this->factory->parseValue($value);

        static::assertGreaterThanOrEqual($time - 1, $result);
        static::assertLessThanOrEqual($time + 1, $result);
    }

    public function testADateBeingPartOfAPropertyWillReplaceWithADate()
    {
        $value = 'some {date:now} data';
        $result = $this->factory->parseValue($value);

        static::assertInternalType('string', $result);
        static::assertRegExp('/^some \d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}\+\d{2}\:\d{2} data$/i', $result);
    }

    public function testADateBeingPartOfAPropertyWillReplaceWithADateWithACustomFormat()
    {
        $value = 'some {date:now:Y-m-d} data';
        $result = $this->factory->parseValue($value);

        static::assertInternalType('string', $result);
        static::assertRegExp('/^some \d{4}\-\d{2}\-\d{2} data$/i', $result);
    }

    public function testAddingAMappingCanTriggerTheMapping()
    {
        $value = 'some {apid:123} here';
        $result = $this->factory->parseValue($value);

        static::assertEquals($value, $result);

        $this->factory->addMapping('/\{apid:(\d+)\}/i', 'Account Profile: \1');

        $result = $this->factory->parseValue($value);

        static::assertEquals('some Account Profile: 123 here', $result);
    }
}
