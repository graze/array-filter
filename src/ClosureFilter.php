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

namespace Graze\ArrayFilter;

use Closure;

class ClosureFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var Closure
     */
    private $function;

    /**
     * @param string  $property
     * @param Closure $function ($value) -> bool
     */
    public function __construct($property, Closure $function)
    {
        $this->property = $property;
        $this->function = $function;
    }

    /**
     * Does this filter match?
     *
     * @param array $data
     *
     * @return bool
     */
    public function matches(array $data)
    {
        return (isset($data[$this->property]) &&
            call_user_func($this->function, $data[$this->property]));
    }
}
