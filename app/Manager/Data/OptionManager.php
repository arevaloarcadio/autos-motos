<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\OptionDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of Option data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class OptionManager extends AbstractManager
{
    /**
     * @var OptionDal
     */
    private $dataAccessLayer;

    /**
     * @param OptionDal $dataAccessLayer
     */
    public function __construct(OptionDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
