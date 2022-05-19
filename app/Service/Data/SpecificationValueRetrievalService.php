<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Data\SpecificationNameEnum;
use App\Input\SpecificationsInput;
use App\Manager\CarAd\CarBodyTypeManager;
use App\Manager\CarAd\CarFuelTypeManager;
use App\Manager\CarAd\CarTransmissionTypeManager;
use App\Manager\CarAd\CarWheelDriveTypeManager;
use App\Manager\Data\TrimManager;
use App\Manager\Data\TrimSpecificationManager;
use App\Models\Specification;
use App\Models\Trim;
use App\Output\SpecificationOptionsWithTrimsOutput;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationValueRetrievalService
{
    /**
     * @var TrimSpecificationManager
     */
    private $trimSpecificationManager;

    /**
     * @var CarFuelTypeManager
     */
    private $carFuelTypeManager;

    /**
     * @var CarTransmissionTypeManager
     */
    private $carTransmissionTypeManager;

    /**
     * @var TrimManager
     */
    private $trimManager;

    /**
     * @var CarBodyTypeManager
     */
    private $carBodyTypeManager;

    /**
     * @var CarWheelDriveTypeManager
     */
    private $carWheelDriveTypeManager;

    public function __construct(
        TrimSpecificationManager $trimSpecificationManager,
        TrimManager $trimManager,
        CarFuelTypeManager $carFuelTypeManager,
        CarTransmissionTypeManager $carTransmissionTypeManager,
        CarBodyTypeManager $carBodyTypeManager,
        CarWheelDriveTypeManager $carWheelDriveTypeManager
    ) {
        $this->trimSpecificationManager   = $trimSpecificationManager;
        $this->carFuelTypeManager         = $carFuelTypeManager;
        $this->carTransmissionTypeManager = $carTransmissionTypeManager;
        $this->trimManager                = $trimManager;
        $this->carBodyTypeManager         = $carBodyTypeManager;
        $this->carWheelDriveTypeManager   = $carWheelDriveTypeManager;
    }

    public function retrieveBodyTypesBySeriesId(
        string $seriesId,
        SpecificationsInput $input
    ): SpecificationOptionsWithTrimsOutput {
        $trims        = $this->retrieveMatchingTrims($seriesId, $input);
        $bodyTypesIds = $this->retrieveSpecValuesFromTrims($trims, SpecificationNameEnum::BODY_TYPE);
        $bodyTypes    = $this->retrieveBodyTypesByIds($bodyTypesIds);

        return new SpecificationOptionsWithTrimsOutput($bodyTypes->toArray(), $trims->toArray());
    }

    public function retrieveFuelTypesBySeriesId(
        string $seriesId,
        SpecificationsInput $input
    ): SpecificationOptionsWithTrimsOutput {
        $trims        = $this->retrieveMatchingTrims($seriesId, $input);
        $fuelTypesIds = $this->retrieveSpecValuesFromTrims($trims, SpecificationNameEnum::FUEL_TYPE);
        $fuelTypes    = $this->retrieveFuelTypesByIds($fuelTypesIds);

        return new SpecificationOptionsWithTrimsOutput($fuelTypes->toArray(), $trims->toArray());
    }

    public function retrieveTransmissionsBySeriesId(
        string $seriesId,
        SpecificationsInput $input
    ): SpecificationOptionsWithTrimsOutput {
        $trims            = $this->retrieveMatchingTrims($seriesId, $input);
        $transmissionsIds = $this->retrieveSpecValuesFromTrims($trims, SpecificationNameEnum::TRANSMISSION);
        $transmissions    = $this->retrieveTransmissionTypesByIds($transmissionsIds);

        return new SpecificationOptionsWithTrimsOutput($transmissions->toArray(), $trims->toArray());
    }

    public function retrieveDriveTypesBySeriesId(string $seriesId, SpecificationsInput $input)
    {
        $trims         = $this->retrieveMatchingTrims($seriesId, $input);
        $driveTypesIds = $this->retrieveSpecValuesFromTrims($trims, SpecificationNameEnum::WHEEL_DRIVE);
        $driveTypes    = $this->retrieveDriveTypesByIds($driveTypesIds);

        return new SpecificationOptionsWithTrimsOutput($driveTypes->toArray(), $trims->toArray());
    }

    private function retrieveMatchingTrims(string $seriesId, SpecificationsInput $input): Collection
    {
        $trims = $this->trimManager->findAllBySeriesId($seriesId);

        if (false === $input->hasSpecifications()) {
            return $trims;
        }

        return $trims
            ->filter(
                function (Trim $trim) use ($input) {
                    $specMatches = [];

                    foreach ($input->getSpecifications() as $alias => $value) {
                        $spec = $trim->specifications->firstWhere(
                            'name',
                            '=',
                            SpecificationNameEnum::getNameByAlias($alias)
                        );

                        $specMatches[$alias] = $spec instanceof Specification && $spec->pivot->value === $value ||
                                               $spec === null;
                    }

                    return false === in_array(false, $specMatches);
                }
            );
    }

    private function retrieveSpecValuesFromTrims(Collection $trims, string $specName): array
    {
        return $trims
            ->map(
                function (Trim $trim) use ($specName) {
                    $spec = $trim->specifications->firstWhere(
                        'name',
                        '=',
                        $specName
                    );
                    if ($spec instanceof Specification) {
                        return $spec->pivot->value;
                    }

                    return null;
                }
            )
            ->filter(
                function (?string $id) {
                    return ! ($id === null);
                }
            )
            ->unique()
            ->toArray();
    }

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    private function retrieveBodyTypesByIds(array $ids): Collection
    {
        if (0 === count($ids)) {
            return $this->carBodyTypeManager->findAllByAdType(AdTypeEnum::AUTO_SLUG);
        }

        return $this->carBodyTypeManager->findAllByIds($ids);

    }

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    private function retrieveTransmissionTypesByIds(array $ids): Collection
    {
        if (0 === count($ids)) {
            return $this->carTransmissionTypeManager->findAllByAdType(AdTypeEnum::AUTO_SLUG);
        }

        return $this->carTransmissionTypeManager->findAllByIds($ids);
    }

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    private function retrieveFuelTypesByIds(array $ids): Collection
    {
        if (0 === count($ids)) {
            return $this->carFuelTypeManager->findAllByAdType(AdTypeEnum::AUTO_SLUG);
        }

        return $this->carFuelTypeManager->findAllByIds($ids);
    }

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    private function retrieveDriveTypesByIds(array $ids): Collection
    {
        if (0 === count($ids)) {
            return $this->carWheelDriveTypeManager->findAllByAdType(AdTypeEnum::AUTO_SLUG);
        }

        return $this->carWheelDriveTypeManager->findAllByIds($ids);
    }
}
