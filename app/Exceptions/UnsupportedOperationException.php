<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class UnsupportedOperationException extends Exception
{
    protected $message = 'Unsupported operation.';
}
