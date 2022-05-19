<?php
declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
trait ImageOutputTrait
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Get the value of the path property.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
