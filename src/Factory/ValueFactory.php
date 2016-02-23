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

use Closure;
use DateTime;

class ValueFactory implements ValueFactoryInterface
{
    /**
     * @var array List of mapping templates to apply '<regex>' => 'value/closure'
     */
    private $mappings = [];

    public function __construct()
    {
        $this->mappings = [
            '/(?<!:\{)\{date:([^\}:]+):?([^\}]+)?\}(?!:\})/i' => function ($matches) {
                $dt = new DateTime($matches[1]);
                $format = isset($matches[2]) ? $matches[2] : 'c';
                return $dt->format($format);
            },
        ];
    }

    /**
     * @param string         $regex
     * @param string|Closure $replace
     *
     * @return $this
     */
    public function addMapping($regex, $replace)
    {
        $this->mappings[$regex] = $replace;

        return $this;
    }

    /**
     * Parse the supplied value and return the interpreted value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        foreach ($this->mappings as $mapping => $replace) {
            if (is_callable($replace)) {
                $value = preg_replace_callback($mapping, $replace, $value);
            } else {
                $value = preg_replace($mapping, $replace, $value);
            }
        }

        return $value;
    }
}
