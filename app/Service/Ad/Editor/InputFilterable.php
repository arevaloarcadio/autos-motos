<?php
declare(strict_types=1);

namespace App\Service\Ad\Editor;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
trait InputFilterable
{
    protected function filterRelevantInput(Model $model, array $input): array
    {
        return array_filter(
            $input,
            function ($key) use ($model) {
                return in_array($key, $model->getFillable());
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
