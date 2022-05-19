<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * @package App\Exceptions
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserAlreadyExistsException extends Exception
{
    protected $message = 'User already exists.';

    protected $code = Response::HTTP_BAD_REQUEST;
}
