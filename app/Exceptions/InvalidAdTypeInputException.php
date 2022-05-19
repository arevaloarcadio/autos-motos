<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * @package App\Exceptions
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class InvalidAdTypeInputException extends Exception
{
    protected $message = 'Invalid ad type input.';
    
    protected $code = Response::HTTP_BAD_REQUEST;
}
