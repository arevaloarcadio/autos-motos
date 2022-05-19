<?php

declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ImageVersionUploadOutput implements IImageOutput
{
    use ImageOutputTrait;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $path, string $type)
    {
        $this->path    = $path;
        $this->type    = $type;
    }

    /**
     * Get the value of the type property.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
