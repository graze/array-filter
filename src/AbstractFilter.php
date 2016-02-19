<?php

namespace Graze\ArrayFilter;

abstract class AbstractFilter implements ArrayFilterInterface
{
    /**
     * Invoke this filter
     *
     * @param array $data
     *
     * @return bool
     */
    public function __invoke(array $data)
    {
        return $this->matches($data);
    }

    /**
     * @inheritdoc
     */
    abstract public function matches(array $data);
}
