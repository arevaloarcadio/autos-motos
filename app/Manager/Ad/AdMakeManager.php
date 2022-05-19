<?php

declare(strict_types=1);

namespace App\Manager\Ad;


use App\DAL\Ad\AdMakeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Models\AdMake;

/**
 * Defines the entity persistence of ad make data transfer objects.
 *
 * @package App\Manager\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdMakeManager extends AbstractManager
{
    /**
     * @var AdMakeDal
     */
    private $dataAccessLayer;

    public function __construct(AdMakeDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    public function findAllByType(string $adType)
    {
        return AdMake::whereAdType(strtoupper($adType))->orderBy('name', 'ASC')->get();
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
