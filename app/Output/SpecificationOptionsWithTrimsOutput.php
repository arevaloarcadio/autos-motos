<?php

declare(strict_types=1);

namespace App\Output;

use App\Models\Trim;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationOptionsWithTrimsOutput implements Arrayable
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var Trim[]
     */
    private $trims;

    /**
     * @param array  $options
     * @param Trim[] $trims
     */
    public function __construct(array $options, array $trims)
    {
        $this->options = $options;
        $this->trims   = $trims;
    }

    /**
     * Get the value of the options property.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get the value of the trims property.
     *
     * @return Trim[]
     */
    public function getTrims(): array
    {
        return $this->trims;
    }

    public function toArray()
    {
        return [
            'options' => array_values($this->getOptions()),
            'trims'   => array_values($this->getTrims()),
        ];
    }
}
