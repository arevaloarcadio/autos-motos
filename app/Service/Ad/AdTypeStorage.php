<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\DTO\AdSubTypeDto;
use App\DTO\AdTypeDto;
use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\TruckTypeEnum;
use App\Exceptions\InvalidAdTypeProvidedException;
use Illuminate\Support\Collection;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdTypeStorage
{
    /**
     * @var Collection
     */
    private $types;

    public function __construct()
    {
        $this->types = collect(
            [
                new AdTypeDto(AdTypeEnum::AUTO_NAME, AdTypeEnum::AUTO_SLUG),
                new AdTypeDto(AdTypeEnum::MOTO_NAME, AdTypeEnum::MOTO_SLUG),
                new AdTypeDto(AdTypeEnum::MOBILE_HOME_NAME, AdTypeEnum::MOBILE_HOME_SLUG),
                new AdTypeDto(AdTypeEnum::MECHANIC_NAME, AdTypeEnum::MECHANIC_SLUG, false),
                new AdTypeDto(AdTypeEnum::RENTAL_NAME, AdTypeEnum::RENTAL_SLUG, false),
                new AdTypeDto(AdTypeEnum::SHOP_NAME, AdTypeEnum::SHOP_SLUG, false),
                new AdTypeDto(
                    AdTypeEnum::TRUCK_NAME,
                    AdTypeEnum::TRUCK_SLUG,
                    true,
                    true,
                    collect(
                        [
                            new AdSubTypeDto(
                                'truck_up_to_7pt5_t',
                                'truck-up-to-7pt5-t',
                                'icons/trucks/truck_up_to.png'
                            ),
                            new AdSubTypeDto(
                                'truck_over_7pt5_t',
                                'truck-over-7pt5-t',
                                'icons/trucks/truck_over.png'
                            ),
                            new AdSubTypeDto(
                                'semi_trailer_truck',
                                'semi-trailer-truck',
                                'icons/trucks/semi_trailer_truck.png'
                            ),
                            new AdSubTypeDto(
                                'trailer',
                                'trailer',
                                'icons/trucks/trailer.png'
                            ),
                            new AdSubTypeDto(
                                'semi_trailer',
                                'semi-trailer',
                                'icons/trucks/semi_trailer.png'
                            ),
                            new AdSubTypeDto(
                                'construction_machine',
                                'construction-machine',
                                'icons/trucks/construction.png'
                            ),
                            new AdSubTypeDto(
                                'bus',
                                'bus',
                                'icons/trucks/bus.png'
                            ),
                            new AdSubTypeDto(
                                'agriculture_vehicle',
                                'agriculture-vehicle',
                                'icons/trucks/agriculture.png'
                            ),
                            new AdSubTypeDto(
                                'forklift',
                                'forklift',
                                'icons/trucks/forklift.png'
                            ),
                        ]
                    )
                ),
            ]
        );
    }

    public function getTypeBySlug(string $slug): AdTypeDto
    {
        $foundType = $this->getTypes()->first(
            function (AdTypeDto $type) use ($slug) {
                return $type->getSlug() === strtolower($slug);
            }
        );

        if ($foundType instanceof AdTypeDto) {
            return $foundType;
        }

        throw new InvalidAdTypeProvidedException();
    }

    public function getSubTypeBySlug(string $slug): AdSubTypeDto
    {
        $foundSubType = $this->getTypes()
                             ->map(
                                 function (AdTypeDto $type) {
                                     if (null === $type->getSubTypes()) {
                                         return null;
                                     }

                                     return $type->getSubTypes();
                                 }
                             )
                             ->flatten()
                             ->filter()
                             ->first(
                                 function (AdSubTypeDto $subType) use ($slug) {
                                     return $subType->getSlug() === $slug;
                                 }
                             );

        if ($foundSubType instanceof AdSubTypeDto) {
            return $foundSubType;
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @return string[]
     */
    public function getAllSubTypeSlugs(): array
    {
        return $this->getTypes()
                    ->map(
                        function (AdTypeDto $type) {
                            if (null === $type->getSubTypes()) {
                                return null;
                            }

                            return $type->getSubTypes()->map(
                                function (AdSubTypeDto $subType) {
                                    return $subType->getSlug();
                                }
                            );
                        }
                    )
                    ->flatten()
                    ->filter()
                    ->values()
                    ->toArray();
    }

    public function getAllSubTypeSlugsByTypeSlug(string $slug): array
    {
        return $this->getTypeBySlug($slug)
                    ->getSubTypes()
                    ->map(
                        function (AdSubTypeDto $subType) {
                            return $subType->getSlug();
                        }
                    )
                    ->values()
                    ->toArray();

    }

    public function getAllVehicleLeavesSlugs(): array
    {
        return $this->getTypes()
                    ->filter(
                        function (AdTypeDto $type) {
                            return $type->isVehicle();
                        }
                    )
                    ->map(
                        function (AdTypeDto $type) {
                            if (null === $type->getSubTypes()) {
                                return collect([$type->getSlug()]);
                            }

                            return $type->getSubTypes()->map(
                                function (AdSubTypeDto $subType) {
                                    return $subType->getSlug();
                                }
                            );
                        }
                    )
                    ->flatten()
                    ->filter()
                    ->values()
                    ->toArray();
    }

    /**
     * Get the value of the types property.
     *
     * @return Collection
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }
}
