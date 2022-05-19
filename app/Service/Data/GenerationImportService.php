<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Generation;
use App\Models\Model;
use Carbon\Carbon;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class GenerationImportService extends AbstractImportService
{
    protected function parseRow(array $row)
    {
        if (Generation::whereExternalId($row[0])->exists()) {
            return;
        }
        $generation = new Generation();
        $generation->model()->associate(Model::whereExternalId($row[1])->first());
        $generation->name                = $row[2];
        $yearBegin                       = $this->sanitizeValue($row[3]);
        $yearEnd                         = $this->sanitizeValue($row[4]);
        $generation->year_begin          = $yearBegin ? (int) $yearBegin : null;
        $generation->year_end            = $yearEnd ? (int) $yearEnd : null;
        $generation->external_id         = $row[0];
        $generation->external_updated_at = $this->convertUpdatedAtTimestamp($row[6], $row[5]);
        $generation->ad_type             = AdTypeEnum::AUTO_SLUG;

        $generation->save();
    }

    protected function getResourceName(): string
    {
        return 'generation';
    }
}
