<?php

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
