<?php
declare(strict_types=1);

namespace App\Manager\User;

use App\DAL\DataAccessLayerInterface;
use App\DAL\User\UserRoleDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of user role data transfer objects.
 *
 * @package App\Manager\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserRoleManager extends AbstractManager
{
    /**
     * @var UserRoleDal
     */
    private $userRoleDal;
    
    /**
     * UserRoleManager constructor.
     *
     * @param UserRoleDal $userRoleDal
     */
    public function __construct(UserRoleDal $userRoleDal)
    {
        $this->userRoleDal = $userRoleDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->userRoleDal;
    }
}
