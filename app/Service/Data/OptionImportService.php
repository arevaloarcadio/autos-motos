<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Option;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class OptionImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Option::whereExternalId($row[0])->exists()) {
            return;
        }
        $option                      = new Option();
        $option->name                = $row[1];
        $option->external_id         = $row[0];
        $option->external_updated_at = $this->convertUpdatedAtTimestamp($row[4], $row[3]);
        $option->ad_type             = AdTypeEnum::AUTO_SLUG;
        $option->slug                = Str::slug(
            sprintf(
                '%s %s',
                $option->name,
                substr(md5(sprintf('%s_%d', $option->ad_type, $option->external_id)), 0, 12)
            )
        );

        $parent = $this->sanitizeValue($row[2]);
        if ($parent !== null) {
            $option->parent()->associate(Option::whereExternalId($parent)->first());
        }

        $option->save();
    }

    protected function getResourceName(): string
    {
        return 'option';
    }
}
