<?php

namespace Graze\ArrayFilter;

interface ArrayFilterInterface
{
    /**
     * Does this filter match?
     *
     * @param array $data Array data
     *
     * @return bool
     */
    public function matches(array $data);
}
