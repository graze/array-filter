<?php

namespace Graze\ArrayFilter\Test\Unit\Factory;

use Graze\ArrayFilter\ArrayFilterInterface;
use Graze\ArrayFilter\Exception\UnknownPropertyDefinitionException;
use Graze\ArrayFilter\Factory\FilterFactory;
use Graze\ArrayFilter\Factory\FilterFactoryInterface;
use Graze\ArrayFilter\Factory\ValueFactoryInterface;
use Graze\ArrayFilter\Test\TestCase;
use Mockery as m;
use Mockery\MockInterface;

class FilterFactoryTest extends TestCase
{
    /**
     * @var FilterFactory
     */
    protected $factory;

    /**
     * @var ValueFactoryInterface|MockInterface
     */
    private $valueFactory;

    public function setUp()
    {
        $this->valueFactory = m::mock(ValueFactoryInterface::class);
        $this->factory = new FilterFactory($this->valueFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(
            FilterFactoryInterface::class,
            $this->factory
        );
    }

    /**
     * @dataProvider getFilterData
     *
     * @param string $property
     * @param mixed  $expected
     * @param array  $metadata
     * @param bool   $result
     */
    public function testGetFilter($property, $expected, $metadata, $result)
    {
        if (is_string($expected)) {
            $this->valueFactory->shouldReceive('parseValue')
                               ->with($expected)
                               ->once()
                               ->andReturn($expected);
        }

        $filter = $this->factory->getFilter($property, $expected);
        static::assertEquals(
            $result,
            $filter->matches($metadata),
            sprintf(
                "Expected %s and %s to %s for property: %s",
                json_encode($expected),
                json_encode($metadata),
                $result,
                $property
            )
        );
    }

    /**
     * @return array
     */
    public function getFilterData()
    {
        return [
            ['test', 'value', ['test' => 'value'], true],
            ['test', 'value', ['test' => 'value2'], false],
            ['test =', 'value', ['test' => 'value'], true],
            ['test =', 'value', ['test' => 'value2'], false],
            ['test >', 10, ['test' => 12], true],
            ['test >', 12, ['test' => 10], false],
            ['test >', 10, ['test' => 10], false],
            ['test >=', 10, ['test' => 12], true],
            ['test >=', 12, ['test' => 10], false],
            ['test >=', 10, ['test' => 10], true],
            ['test <', 10, ['test' => 12], false],
            ['test <', 12, ['test' => 10], true],
            ['test <', 10, ['test' => 10], false],
            ['test <=', 10, ['test' => 12], false],
            ['test <=', 12, ['test' => 10], true],
            ['test <=', 10, ['test' => 10], true],
            ['test !=', 'value', ['test' => 'other'], true],
            ['test !=', 'value', ['test' => 'value'], false],
            ['test <>', 'value', ['test' => 'other'], true],
            ['test <>', 'value', ['test' => 'value'], false],
            ['test ~', '/word\d+stuff/i', ['test' => 'word12346234stuFF'], true],
            ['test ~', '/word\d+stuff/', ['test' => 'word12346234stuFF'], false],
            ['test ~=', '/word\d+stuff/i', ['test' => 'word12346234stuFF'], true],
            ['test ~=', '/word\d+stuff/', ['test' => 'word12346234stuFF'], false],
            ['test in', ['1', '2'], ['test' => '1'], true],
            ['test in', ['1', '2'], ['test' => '2'], true],
            ['test in', ['1', '2'], ['test' => '3'], false],
        ];
    }

    /**
     * @dataProvider invalidPropertyNames
     *
     * @param string $property
     */
    public function testGetFilterWillThrowExceptionWithInvalidProperty($property)
    {
        $this->expectException(UnknownPropertyDefinitionException::class);

        $this->factory->getFilter($property, '');
    }

    /**
     * @return array
     */
    public function invalidPropertyNames()
    {
        return [
            ['test = '],
            ['test jdksla'],
            ['  something'],
            ['test !!'],
            ['stuff space ='],
            ['things <<'],
            ['stuff >>'],
            [''],
        ];
    }

    /**
     * @dataProvider createFiltersTestData
     *
     * @param array $configuration
     * @param array $metadata
     * @param bool  $result
     */
    public function testCreateFilters(array $configuration, array $metadata, $result)
    {
        foreach ($configuration as $property => $value) {
            $this->valueFactory->shouldReceive('parseValue')
                               ->with($value)
                               ->andReturn($value);
        }

        $filter = $this->factory->createFilters($configuration);
        static::assertEquals(
            $result,
            $filter->matches($metadata),
            sprintf(
                "Expected configuration: %s and data: %s to result in: %s",
                json_encode($configuration),
                json_encode($metadata),
                $result
            )
        );
    }

    /**
     * @return array
     */
    public function createFiltersTestData()
    {
        return [
            [['test' => 'value'], ['test' => 'value'], true],
            [['test' => 'value', 'test2' => 'value'], ['test' => 'value', 'test2' => 'value'], true],
            [['test' => 'value', 'test2' => 'value'], ['test' => 'value', 'test2' => 'value2'], false],
            [['test >' => 4, 'test <' => 8], ['test' => 6], true],
            [['test >' => 4, 'test <' => 8], ['test' => 2], false],
        ];
    }

    public function testStaticAccessors()
    {
        $filter = FilterFactory::filter('name', 'value');
        static::assertInstanceOf(ArrayFilterInterface::class, $filter);
        static::assertTrue($filter->matches(['name' => 'value']));
        static::assertFalse($filter->matches(['name' => 'value2']));

        $filter = FilterFactory::fromConfiguration([
            'name' => 'value2',
        ]);
        static::assertInstanceOf(ArrayFilterInterface::class, $filter);
        static::assertTrue($filter->matches(['name' => 'value2']));
        static::assertFalse($filter->matches(['name' => 'value3']));
    }
}
