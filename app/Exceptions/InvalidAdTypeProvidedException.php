<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * @package App\Exceptions
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class InvalidAdTypeProvidedException extends Exception
{
    protected $message = 'Invalid ad type provided.';
    
    protected $code = Response::HTTP_NOT_FOUND;
}
