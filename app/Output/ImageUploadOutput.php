<?php

declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ImageUploadOutput implements IImageOutput
{
    use ImageOutputTrait;

    /**
     * @var ImageVersionUploadOutput[]
     */
    private $versions;

    /**
     * @param string                     $path
     * @param ImageVersionUploadOutput[] $versions
     */
    public function __construct(string $path, array $versions)
    {
        $this->path     = $path;
        $this->versions = $versions;
    }

    /**
     * Get the value of the versions property.
     *
     * @return ImageVersionUploadOutput[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}
