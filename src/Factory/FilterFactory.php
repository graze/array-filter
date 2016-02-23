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
        return self::getInstance()->createFilters($configuration);
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
        return self::getInstance()->getFilter($property, $value);
    }

    /**
     * @return FilterFactory
     */
    private static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new FilterFactory(new ValueFactory());
        }
        return self::$instance;
    }
}
