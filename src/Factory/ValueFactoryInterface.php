<?php

namespace Graze\ArrayFilter\Factory;

interface ValueFactoryInterface
{
    /**
     * Parse the supplied value and return the interpreted value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value);
}
