<?php
declare(strict_types=1);

namespace App\Input;

use Illuminate\Http\Request;

/**
 * Defines the modelling of a request metadata input.
 *
 * @package App\Input
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class RequestMetadataInput
{
    /**
     * @var string[]
     */
    private $load = [];
    
    /**
     * RequestMetadataInput constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $loadString = $request->get('load', null);
        if (null !== $loadString) {
            $this->load = explode(',', $loadString);
        }
    }
    
    /**
     * Get the value of the load property.
     *
     * @return string[]
     */
    public function getLoad(): array
    {
        return $this->load;
    }
}
