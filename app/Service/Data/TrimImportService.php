<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Models;
use App\Models\Series;
use App\Models\Trim;
use Carbon\Carbon;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TrimImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Trim::whereExternalId($row[0])->exists()) {
            return;
        }

        $trim = new Trim();
        $trim->series()->associate(Series::whereExternalId($row[1])->first());
        $trim->model()->associate(Model::whereExternalId($row[2])->first());
        $trim->name                  = $row[3];
        $yearStart                   = $this->sanitizeValue($row[4]);
        $yearEnd                     = $this->sanitizeValue($row[5]);
        $trim->production_year_start = $yearStart ? (int) $yearStart : null;
        $trim->production_year_end   = $yearEnd ? (int) $yearEnd : null;
        $trim->ad_type               = AdTypeEnum::AUTO_SLUG;
        $trim->external_id           = $row[0];
        $trim->external_updated_at   = $this->convertUpdatedAtTimestamp($row[7], $row[6]);

        $trim->save();
    }

    protected function getResourceName(): string
    {
        return 'trim';
    }
}
