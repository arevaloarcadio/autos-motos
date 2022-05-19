<?php
declare(strict_types=1);

namespace App\Manager\User;

use App\DAL\DataAccessLayerInterface;
use App\DAL\User\RoleDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of role data transfer objects.
 *
 * @package App\Manager\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class RoleManager extends AbstractManager
{
    /**
     * @var RoleDal
     */
    private $roleDal;
    
    /**
     * RoleManager constructor.
     *
     * @param RoleDal $roleDal
     */
    public function __construct(RoleDal $roleDal)
    {
        $this->roleDal = $roleDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->roleDal;
    }
}
