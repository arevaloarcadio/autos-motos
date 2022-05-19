<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Equipment;
use App\Models\EquipmentOption;
use App\Models\Option;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class EquipmentOptionImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (EquipmentOption::whereExternalId($row[0])->exists()) {
            return;
        }

        $equipmentOption = new EquipmentOption();
        $equipmentOption->option()->associate(Option::whereExternalId($row[1])->first());
        $equipmentOption->equipment()->associate(Equipment::whereExternalId($row[2])->first());
        $equipmentOption->is_base             = $row[3] === '1';
        $equipmentOption->ad_type             = AdTypeEnum::AUTO_SLUG;
        $equipmentOption->external_id         = (int) $row[0];
        $equipmentOption->external_updated_at = $this->convertUpdatedAtTimestamp($row[5], $row[4]);

        try {
            $equipmentOption->save();
        } catch (\Throwable $exception) {
            dd($row);
        }
    }

    protected function getResourceName(): string
    {
        return 'optionValue';
    }
}
