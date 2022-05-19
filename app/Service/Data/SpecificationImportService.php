<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Specification;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Specification::whereExternalId($row[0])->exists()) {
            return;
        }

        $specification       = new Specification();
        $specification->name = $row[1];
        $parent              = $this->sanitizeValue($row[2]);
        if ($parent !== null) {
            $specification->parent()->associate(Specification::whereExternalId($parent)->first());
        }
        $specification->ad_type             = AdTypeEnum::AUTO_SLUG;
        $specification->external_id         = (int) $row[0];
        $specification->external_updated_at = $this->convertUpdatedAtTimestamp($row[4], $row[3]);
        $specification->slug = Str::slug(
            sprintf(
                '%s %s',
                $specification->name,
                substr(md5(sprintf('%s_%d', $specification->ad_type, $specification->external_id)), 0, 12)
            )
        );

        $specification->save();
    }

    protected function getResourceName(): string
    {
        return 'specification';
    }
}
