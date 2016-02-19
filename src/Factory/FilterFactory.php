<?php

namespace Graze\ArrayFilter\Factory;

use Graze\ArrayFilter\AllOfFilter;
use Graze\ArrayFilter\ArrayFilterInterface;
use Graze\ArrayFilter\ClosureFilter;
use Graze\ArrayFilter\Exception\UnknownPropertyDefinitionException;

/**
 * Factory class to interpret string filter configuration into different configurations
 *
 * Currently supports:
 *
 * ```
 * [
 *     'param' => 'value',    // $data['param'] == 'value';
 *     'param =' => 'value',  // $data['param'] == 'value';
 *     'param ~' => 'value',  // preg_match($value, $data['param']);
 *     'param >' => 'value',  // $data['param'] > 'value';
 *     'param >=' => 'value', // $data['param'] >= 'value';
 *     'param <' => 'value',  // $data['param'] < 'value';
 *     'param <=' => 'value', // $data['param'] <= 'value';
 *     'param !=' => 'value', // $data['param'] != 'value';
 *     'param <>' => 'value', // $data['param'] != 'value';
 *     'param in' => ['value1','value2'], // in_array($data['param'], ['value1','value2'])
 * ]
 * ```
 */
class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var ValueFactoryInterface
     */
    protected $valueFactory;

    /**
     * @var FilterFactory
     */
    static private $instance = null;

    /**
     * Build the definitions
     *
     * @param ValueFactoryInterface $valueFactory
     */
    public function __construct(ValueFactoryInterface $valueFactory)
    {
        $this->valueFactory = $valueFactory;
        $this->definitions = [
            '/^(\w+)(\s*=)?$/i'    => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual == $expected;
                });
            },
            '/^(\w+)\s*~=?$/i'     => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return preg_match($expected, $actual);
                });
            },
            '/^(\w+)\s*>$/i'       => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual > $expected;
                });
            },
            '/^(\w+)\s*>=$/i'      => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual >= $expected;
                });
            },
            '/^(\w+)\s*<$/i'       => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual < $expected;
                });
            },
            '/^(\w+)\s*<=$/i'      => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual <= $expected;
                });
            },
            '/^(\w+)\s*(<>|!=)$/i' => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual != $expected;
                });
            },
            '/^(\w+)\s*in$/i'      => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return in_array($actual, $expected);
                });
            },
        ];
    }

    /**
     * @param array $configuration
     *
     * @return ArrayFilterInterface
     */
    public function createFilters(array $configuration)
    {
        $filters = [];
        foreach ($configuration as $property => $value) {
            $expected = is_string($value) ? $this->valueFactory->parseValue($value) : $value;
            $filters[] = $this->getFilter($property, $expected);
        }

        return new AllOfFilter($filters);
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return ArrayFilterInterface
     * @throws UnknownPropertyDefinitionException
     */
    public function getFilter($property, $value)
    {
        foreach ($this->definitions as $key => $definition) {
            if (preg_match($key, $property, $matches)) {
                $expected = is_string($value) ? $this->valueFactory->parseValue($value) : $value;
                return call_user_func($definition, $matches[1], $expected);
            }
        }

        throw new UnknownPropertyDefinitionException($property);
    }

    /**
     * @param array $configuration
     *
     * @return ArrayFilterInterface
     */
    public static function fromConfiguration(array $configuration)
    {
        return static::getInstance()->createFilters($configuration);
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return ArrayFilterInterface
     * @throws UnknownPropertyDefinitionException
     */
    public static function filter($property, $value)
    {
        return static::getInstance()->getFilter($property, $value);
    }

    /**
     * @return FilterFactory
     */
    private static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new FilterFactory(new ValueFactory());
        }
        return static::$instance;
    }
}
