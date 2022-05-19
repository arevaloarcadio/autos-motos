<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Equipment;
use App\Models\Trim;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class EquipmentImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Equipment::whereExternalId($row[0])->exists()) {
            return;
        }

        $equipment = new Equipment();
        $equipment->trim()->associate(Trim::whereExternalId($row[1])->first());
        $equipment->name                = $row[2];
        $equipment->year                = $this->sanitizeValue($row[3]);
        $equipment->ad_type             = AdTypeEnum::AUTO_SLUG;
        $equipment->external_id         = $row[0];
        $equipment->external_updated_at = $this->convertUpdatedAtTimestamp($row[5], $row[4]);

        try {
            $equipment->save();
        } catch (\Throwable $exception) {
            dd($row);
        }
    }

    protected function getResourceName(): string
    {
        return 'equipment';
    }
}
