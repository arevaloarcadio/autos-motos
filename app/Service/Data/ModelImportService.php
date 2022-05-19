<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Make;
use App\Models\Model;
use Illuminate\Support\Str;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ModelImportService extends AbstractImportService
{

    protected function parseRow(array $row)
    {
        if (Model::whereExternalId($row[0])->exists()) {
            return;
        }
        $model = new Model();
        $model->make()->associate(Make::whereExternalId($row[1])->first());
        $model->name                = $row[2];
        $model->external_id         = $row[0];
        $model->external_updated_at = $this->convertUpdatedAtTimestamp($row[4], $row[3]);
        $model->ad_type             = AdTypeEnum::AUTO_SLUG;
        $model->slug                = Str::slug(
            sprintf(
                '%s %s',
                $model->name,
                substr(md5(sprintf('%s_%s%d', $model->make->name, $model->ad_type, $model->external_id)), 0, 12)
            )
        );

        $model->save();
    }

    protected function getResourceName(): string
    {
        return 'model';
    }
}
