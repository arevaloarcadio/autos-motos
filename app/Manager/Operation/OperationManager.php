<?php

declare(strict_types=1);

namespace App\Manager\Operation;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Operation\OperationDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of operation data transfer objects.
 *
 * @package App\Manager\Operation
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class OperationManager extends AbstractManager
{
    /**
     * @var OperationDal
     */
    private $operationDal;

    public function __construct(OperationDal $operationDal)
    {
        $this->operationDal = $operationDal;
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->operationDal;
    }
}
