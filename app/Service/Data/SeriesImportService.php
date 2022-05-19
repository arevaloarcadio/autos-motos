<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Generation;
use App\Models\Model;
use App\Models\Series;
use Carbon\Carbon;

/**
 * Class SeriesImportService.
 *
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SeriesImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Series::whereExternalId($row[0])->exists()) {
            return;
        }

        $series = new Series();
        $series->model()->associate(Model::whereExternalId($row[1])->first());
        if ($generation = Generation::whereExternalId($row[2])->first()) {
            $series->generation()->associate($generation);
        }
        $series->name                = $row[3];
        $series->ad_type             = AdTypeEnum::AUTO_SLUG;
        $series->external_id         = $row[0];
        $series->external_updated_at = $this->convertUpdatedAtTimestamp($row[5], $row[4]);

        $series->save();
    }

    protected function getResourceName(): string
    {
        return 'serie';
    }
}
