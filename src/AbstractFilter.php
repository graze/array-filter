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
     * {@inheritdoc}
     *
     * @param array $data
     *
     * @return bool
     */
    abstract public function matches(array $data);
}
