<?php

namespace Graze\ArrayFilter;

interface ArrayFilterFactoryInterface
{
    /**
     * @param array $configuration
     * @return ArrayFilterInterface
     */
    public function createFilters(array $configuration);

    /**
     * @param string $property
     * @param mixed $value
     * @return ArrayFilterInterface
     */
    public function createFilter($property, $value);
}
