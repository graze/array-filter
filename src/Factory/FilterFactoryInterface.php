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
