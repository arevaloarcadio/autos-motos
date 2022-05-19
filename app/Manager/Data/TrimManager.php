<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\TrimDal;
use App\Enum\Data\SpecificationNameEnum;
use App\Manager\AbstractManager;
use App\Models\Trim;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of Trim data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TrimManager extends AbstractManager
{
    /**
     * @var TrimDal
     */
    private $dataAccessLayer;

    /**
     * @param TrimDal $dataAccessLayer
     */
    public function __construct(TrimDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * @param string $seriesId
     *
     * @return Collection
     */
    public function findAllBySeriesId(string $seriesId): Collection
    {
        return $this->dataAccessLayer->findAllBySeriesId($seriesId);
    }

    public function findAllBySeriesIdAndSpecs(string $seriesId, array $specs): Collection
    {
        $groupByConditions = [
            'sql'      => [],
            'bindings' => [],
        ];
        $specNamesUsed     = [];
        foreach ($specs as $key => $value) {
            if ( ! ($value === null) && $specName = SpecificationNameEnum::getNameByAlias($key)) {
                $groupByConditions['sql'][]      = 'SUM(s.name = ? AND ts.value = ?) > 0';
                $groupByConditions['bindings'][] = $specName;
                $groupByConditions['bindings'][] = $value;
                $specNamesUsed[]                 = $specName;
            }
        }
        $query = Trim::query()
                     ->select('trims.*')
                     ->whereIn(
                         'id',
                         function (Builder $query) use ($groupByConditions, $specNamesUsed, $seriesId) {
                             $query->select('ts.trim_id')
                                   ->from('trim_specifications as ts')
                                   ->join(
                                       'trims as t',
                                       function (JoinClause $join) use ($seriesId) {
                                           $join->on('ts.trim_id', '=', 't.id')
                                                ->where('t.series_id', '=', $seriesId);
                                       }
                                   )
                                   ->join(
                                       'specifications as s',
                                       function (JoinClause $join) use ($specNamesUsed) {
                                           $join->on('ts.specification_id', '=', 's.id')
                                                ->whereIn('s.name', $specNamesUsed);
                                       }
                                   )
                                   ->groupBy('ts.trim_id')
                                   ->havingRaw(
                                       implode(' AND ', $groupByConditions['sql']),
                                       $groupByConditions['bindings']
                                   );
                         }
                     )
                     ->where('trims.series_id', '=', $seriesId)
                     ->orderBy('trims.name', 'ASC');

        return $query->get();
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
