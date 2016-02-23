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

namespace Graze\ArrayFilter\Exception;

use Exception;

class UnknownPropertyDefinitionException extends Exception
{
    public function __construct($property, $message = '', Exception $previous = null)
    {
        $message = "Unknown property definition: $property. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
