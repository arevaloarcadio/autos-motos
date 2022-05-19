<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\MakeDal;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;
use App\Models\Make;

/**
 * Defines the entity persistence of make data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MakeManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var MakeDal
     */
    private $dataAccessLayer;

    /**
     * @param MakeDal $dataAccessLayer
     */
    public function __construct(MakeDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
