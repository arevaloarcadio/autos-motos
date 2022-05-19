<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Make;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MakeImportService extends AbstractImportService
{
    protected function parseRow(array $row)
    {
        if (Make::whereExternalId($row[0])->exists()) {
            return;
        }
        $make                      = new Make();
        $make->external_id         = $row[0];
        $make->name                = $row[1];
        $make->external_updated_at = $this->convertUpdatedAtTimestamp($row[3], $row[2]);
        $make->ad_type             = AdTypeEnum::AUTO_SLUG;
        $make->slug                = Str::slug(
            sprintf(
                '%s %s',
                $make->name,
                substr(md5(sprintf('%s_%d', $make->ad_type, $make->external_id)), 0, 12)
            )
        );

        $make->save();
    }

    protected function getResourceName(): string
    {
        return 'make';
    }
}
