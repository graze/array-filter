<?php

namespace Graze\ArrayFilter\Factory;

use Graze\ArrayFilter\ArrayFilterInterface;

interface FilterFactoryInterface
{
    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return ArrayFilterInterface
     */
    public function getFilter($property, $value);

    /**
     * Build a filter based on a configuration array
     *
     * @param array $configuration
     *
     * @return mixed
     */
    public function createFilters(array $configuration);
}
