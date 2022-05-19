<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\SpecificationDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of Specification data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationManager extends AbstractManager
{
    /**
     * @var SpecificationDal
     */
    private $dataAccessLayer;

    /**
     * @param SpecificationDal $dataAccessLayer
     */
    public function __construct(SpecificationDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
