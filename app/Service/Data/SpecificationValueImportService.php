<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Specification;
use App\Models\Trim;
use App\Models\TrimSpecification;
use Carbon\Carbon;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationValueImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (TrimSpecification::whereExternalId($row[0])->exists()) {
            return;
        }

        $trimSpecification = new TrimSpecification();
        $trimSpecification->trim()->associate(Trim::whereExternalId($row[1])->first());
        $trimSpecification->specification()->associate(Specification::whereExternalId($row[2])->first());
        $trimSpecification->value               = $row[3];
        $trimSpecification->unit                = $this->sanitizeValue($row[4]);
        $trimSpecification->ad_type             = AdTypeEnum::AUTO_SLUG;
        $trimSpecification->external_id         = (int) $row[0];
        $trimSpecification->external_updated_at = $this->convertUpdatedAtTimestamp($row[6], $row[5]);

        $trimSpecification->save();
    }

    protected function getResourceName(): string
    {
        return 'specificationValue';
    }
}
