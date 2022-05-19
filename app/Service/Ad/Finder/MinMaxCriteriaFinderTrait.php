<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;


use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
trait MinMaxCriteriaFinderTrait
{
    protected function applyMinMaxCriteriaSearch(
        Builder $query,
        array $input,
        string $inputKey,
        string $fieldName
    ): Builder {
        $minInputName = sprintf('%s_min', $inputKey);
        $maxInputName = sprintf('%s_max', $inputKey);
        if (isset($input[$minInputName])) {
            $query->where($fieldName, '>=', $input[$minInputName]);
        }
        if (isset($input[$maxInputName])) {
            $query->where($fieldName, '<=', $input[$maxInputName]);
        }

        return $query;
    }
}
