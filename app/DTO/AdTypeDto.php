<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

/**
 * @package App\DTO
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdTypeDto implements Jsonable
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
     * @var bool
     */
    private $hasSubTypes;

    /**
     * @var bool
     */
    private $isVehicle;

    /**
     * @var Collection|null
     */
    private $subTypes;

    public function __construct(
        string $name,
        string $slug,
        bool $isVehicle = true,
        bool $hasSubTypes = false,
        Collection $subTypes = null
    ) {
        $this->name        = $name;
        $this->slug        = $slug;
        $this->hasSubTypes = $hasSubTypes;
        $this->isVehicle   = $isVehicle;
        $this->subTypes    = $subTypes;
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
     * Get the value of the hasSubTypes property.
     *
     * @return bool
     */
    public function hasSubTypes(): bool
    {
        return $this->hasSubTypes;
    }

    /**
     * Get the value of the isVehicle property.
     *
     * @return bool
     */
    public function isVehicle(): bool
    {
        return $this->isVehicle;
    }

    /**
     * Get the value of the subTypes property.
     *
     * @return Collection|null
     */
    public function getSubTypes(): ?Collection
    {
        return $this->subTypes;
    }

    public function toJson($options = 0)
    {
        return json_encode(
            [
                'name'         => $this->getName(),
                'slug'         => $this->getSlug(),
                'has_subtypes' => $this->hasSubTypes(),
                'is_vehicle'   => $this->isVehicle(),
                'subtypes'     => $this->getSubTypes() ?? [],
            ]
        );
    }
}
