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

class OneOfFilter extends AbstractFilter
{
    /**
     * @var ArrayFilterInterface[]
     */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * @param ArrayFilterInterface $filter
     */
    public function addFilter(ArrayFilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Loop through all the filters and only return if all match
     *
     * @param array $data
     *
     * @return bool
     */
    public function matches(array $data)
    {
        foreach ($this->filters as $filter) {
            if ($filter->matches($data)) {
                return true;
            }
        }

        return false;
    }
}
