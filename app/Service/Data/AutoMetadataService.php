<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Console\Commands\Data\SyncAutoMetadataCommand;
use App\Enum\Data\SpecificationNameEnum;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\CarWheelDriveType;
use App\Models\TrimSpecification;
use App\Service\HasCommandOutputs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoMetadataService
{
    use HasCommandOutputs;

    public function syncAll(SyncAutoMetadataCommand $command): void
    {
        //        $this->syncBodyTypes($command);
        $this->syncFuelTypes($command);
        $this->syncDriveTypes($command);
        $this->syncTransmissionTypes($command);
    }

    public function syncBodyTypes(SyncAutoMetadataCommand $command): void
    {
        $totalItems = $this->getValuesBySpecificationNameQuery(SpecificationNameEnum::BODY_TYPE)
                           ->count();
        $this->outputProgress($command, sprintf('Starting to process body types. Total items: %d', $totalItems));
        $this->processValuesBySpecificationName(
            SpecificationNameEnum::BODY_TYPE,
            function (Collection $rows) use ($command, &$totalItems) {
                /** @var TrimSpecification $row */
                foreach ($rows as $row) {
                    if (Str::isUuid($row->value)) {
                        continue;
                    }

                    $internalName = Str::slug($row->value, '_');
                    /** @var CarBodyType $existing */
                    if ($existing = CarBodyType::where('internal_name', $internalName)->first()) {
                        $row->value = $existing->id;

                        $row->save();

                        continue;
                    }

                    $bodyType                = new CarBodyType();
                    $bodyType->internal_name = $internalName;
                    $bodyType->slug          = Str::slug($row->value);
                    $bodyType->icon_url      = sprintf('icons/%s.png', $internalName);
                    $bodyType->external_name = $row->value;

                    $bodyType->save();

                    $row->value = $bodyType->id;

                    $row->save();
                }
                $totalItems = $totalItems - 100 < 0 ? 0 : $totalItems - 100;
                $this->outputProgress($command, sprintf('Remaining items: %d', $totalItems));
            }
        );
        $this->outputProgress($command, 'DONE.');
    }

    public function syncFuelTypes(SyncAutoMetadataCommand $command): void
    {
        $totalItems = $this->getValuesBySpecificationNameQuery(SpecificationNameEnum::FUEL_TYPE)
                           ->count();
        $this->outputProgress($command, sprintf('- Starting to process fuel types. Total items: %d', $totalItems));
        $this->processValuesBySpecificationName(
            SpecificationNameEnum::FUEL_TYPE,
            function (Collection $rows) use ($command, &$totalItems) {
                /** @var TrimSpecification $row */
                foreach ($rows as $row) {
                    if (Str::isUuid($row->value)) {
                        continue;
                    }

                    $internalName = Str::slug($row->value, '_');
                    /** @var CarFuelType $existing */
                    if ($existing = CarFuelType::where('internal_name', $internalName)->first()) {
                        $row->value = $existing->id;

                        $row->save();

                        continue;
                    }

                    $fuelType                = new CarFuelType();
                    $fuelType->internal_name = $internalName;
                    $fuelType->slug          = Str::slug($row->value);
                    $fuelType->external_name = $row->value;

                    $fuelType->save();

                    $row->value = $fuelType->id;

                    $row->save();
                }
                $totalItems = $totalItems - 100 < 0 ? 0 : $totalItems - 100;
                $this->outputProgress($command, sprintf('-- Remaining items: %d', $totalItems));
            }
        );
        $this->outputProgress($command, '- DONE.');
    }

    public function syncDriveTypes(SyncAutoMetadataCommand $command): void
    {
        $totalItems = $this->getValuesBySpecificationNameQuery(SpecificationNameEnum::WHEEL_DRIVE)
                           ->count();
        $this->outputProgress($command, sprintf('- Starting to process drive types. Total items: %d', $totalItems));
        $this->processValuesBySpecificationName(
            SpecificationNameEnum::WHEEL_DRIVE,
            function (Collection $rows) use ($command, &$totalItems) {
                /** @var TrimSpecification $row */
                foreach ($rows as $row) {
                    if (Str::isUuid($row->value)) {
                        continue;
                    }

                    $internalName = Str::slug($row->value, '_');
                    /** @var CarWheelDriveType $existing */
                    if ($existing = CarWheelDriveType::where('internal_name', $internalName)->first()) {
                        $row->value = $existing->id;

                        $row->save();

                        continue;
                    }

                    $driveType                = new CarWheelDriveType();
                    $driveType->internal_name = $internalName;
                    $driveType->slug          = Str::slug($row->value);
                    $driveType->external_name = $row->value;

                    $driveType->save();

                    $row->value = $driveType->id;

                    $row->save();
                }
                $totalItems = $totalItems - 100 < 0 ? 0 : $totalItems - 100;
                $this->outputProgress($command, sprintf('-- Remaining items: %d', $totalItems));
            }
        );
        $this->outputProgress($command, '- DONE.');
    }

    public function syncTransmissionTypes(SyncAutoMetadataCommand $command): void
    {
        $totalItems = $this->getValuesBySpecificationNameQuery(SpecificationNameEnum::TRANSMISSION)
                           ->count();
        $this->outputProgress(
            $command,
            sprintf('- Starting to process transmission types. Total items: %d', $totalItems)
        );
        $this->processValuesBySpecificationName(
            SpecificationNameEnum::TRANSMISSION,
            function (Collection $rows) use ($command, &$totalItems) {
                /** @var TrimSpecification $row */
                foreach ($rows as $row) {
                    if (Str::isUuid($row->value)) {
                        continue;
                    }

                    $internalName = Str::slug($row->value, '_');
                    /** @var CarTransmissionType $existing */
                    if ($existing = CarTransmissionType::where('internal_name', $internalName)->first()) {
                        $row->value = $existing->id;

                        $row->save();

                        continue;
                    }

                    $transmissionType                = new CarTransmissionType();
                    $transmissionType->internal_name = $internalName;
                    $transmissionType->slug          = Str::slug($row->value);
                    $transmissionType->external_name = $row->value;

                    $transmissionType->save();

                    $row->value = $transmissionType->id;

                    $row->save();
                }
                $totalItems = $totalItems - 100 < 0 ? 0 : $totalItems - 100;
                $this->outputProgress($command, sprintf('-- Remaining items: %d', $totalItems));
            }
        );
        $this->outputProgress($command, '- DONE.');
    }

    private function processValuesBySpecificationName(string $specificationName, callable $callable): void
    {
        $this->getValuesBySpecificationNameQuery($specificationName)
             ->orderBy('id', 'ASC')
             ->chunkById(100, $callable, 'trim_specifications.id', 'id');
    }

    private function getValuesBySpecificationNameQuery(string $specificationName): Builder
    {
        return TrimSpecification::query()
                                ->select('trim_specifications.*')
                                ->join('specifications as s', 'specification_id', '=', 's.id')
                                ->where('s.name', $specificationName);
    }
}
