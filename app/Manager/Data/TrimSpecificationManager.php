<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\TrimDal;
use App\Enum\Data\SpecificationNameEnum;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use App\Models\TrimSpecification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TrimSpecificationManager
{
    public function findDistinctValuesBySeriesAndSpecification(string $seriesId, string $specificationName): array
    {
        $result = TrimSpecification::query()
                                   ->select('trim_specifications.value')
                                   ->join('trims as t', 'trim_specifications.trim_id', '=', 't.id')
                                   ->join('specifications as s', 'trim_specifications.specification_id', '=', 's.id')
                                   ->where('t.series_id', '=', $seriesId)
                                   ->where('s.name', '=', $specificationName)
                                   ->orderBy('trim_specifications.value', 'ASC')
                                   ->get();

        $values = $result->map(
            function (TrimSpecification $row) {
                return $row->value;
            }
        );

        return $values->toArray();
    }

    public function findValuesBySeriesAndOtherSpecificationValue(
        string $seriesId,
        string $specificationName,
        array $otherSpecs
    ): array {
        $groupByConditions = [
            'sql'      => [],
            'bindings' => [],
        ];
        $specNamesUsed     = [];
        foreach ($otherSpecs as $key => $value) {
            if ( ! ($value === null) && $specName = SpecificationNameEnum::getNameByAlias($key)) {
                $groupByConditions['sql'][]      = 'SUM(s2.name = ? AND ts2.value = ?) > 0';
                $groupByConditions['bindings'][] = $specName;
                $groupByConditions['bindings'][] = $value;
                $specNamesUsed[]                 = $specName;
            }
        }
        $result = TrimSpecification::query()
                                   ->select('ts.value')
                                   ->from('trim_specifications as ts')
                                   ->join('specifications as s', 'ts.specification_id', '=', 's.id')
                                   ->whereIn(
                                       'ts.trim_id',
                                       function (Builder $query) use ($seriesId, $specNamesUsed, $groupByConditions) {
                                           $query->select('ts2.trim_id')
                                                 ->from('trim_specifications as ts2')
                                                 ->join(
                                                     'trims as t',
                                                     function (JoinClause $join) use ($seriesId) {
                                                         $join->on('ts2.trim_id', '=', 't.id')
                                                              ->where('t.series_id', '=', $seriesId);
                                                     }
                                                 )
                                                 ->join(
                                                     'specifications as s2',
                                                     function (JoinClause $join) use ($specNamesUsed) {
                                                         $join->on('ts2.specification_id', '=', 's2.id')
                                                              ->whereIn('s2.name', $specNamesUsed);
                                                     }
                                                 )
                                                 ->groupBy('ts2.trim_id')
                                                 ->havingRaw(
                                                     implode(' AND ', $groupByConditions['sql']),
                                                     $groupByConditions['bindings']
                                                 );
                                       }
                                   )
                                   ->where('s.name', '=', $specificationName)
                                   ->get();
        $values = $result->map(
            function (TrimSpecification $row) {
                return $row->value;
            }
        );

        return $values->toArray();
    }
}
