<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Storage;

/**
 * Defines the modelling of the ad sub type data transfer object.
 *
 * @package App\DTO
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdSubTypeDto implements Jsonable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string|null
     */
    private $iconPath;

    public function __construct(string $name, string $slug, string $iconPath = null)
    {
        $this->name     = $name;
        $this->slug     = $slug;
        $this->iconPath = $iconPath;
    }

    /**
     * Get the value of the name property.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the slug property.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the value of the iconPath property.
     *
     * @return string|null
     */
    public function getIconPath(): ?string
    {
        if (null === $this->iconPath) {
            return null;
        }

        return Storage::disk('s3')->url($this->iconPath);
    }

    public function toJson($options = 0)
    {
        return json_encode(
            [
                'name'      => $this->getName(),
                'slug'      => $this->getSlug(),
                'icon_path' => $this->getIconPath(),
            ]
        );
    }
}
