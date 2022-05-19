<?php

declare(strict_types=1);

namespace App\Manager\Ad;


use App\DAL\Ad\AdModelDal;
use App\Manager\AbstractManager;
use App\Models\AdModel;

/**
 * Defines the entity persistence of ad model data transfer objects.
 *
 * @package App\Manager\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdModelManager extends AbstractManager
{
    /**
     * @var AdModelDal
     */
    private $dataAccessLayer;

    public function __construct(AdModelDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    public function findAllMainByMakeId(string $makeId)
    {
        return AdModel::with('children')->whereAdMakeId($makeId)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
