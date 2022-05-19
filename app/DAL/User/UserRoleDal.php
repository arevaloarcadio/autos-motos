<?php
declare(strict_types=1);

namespace App\DAL\User;

use App\DAL\AbstractEloquentDal;
use App\Models\UserRole;

/**
 * Defines the data access layer operations for user role.
 *
 * @package App\DAL\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserRoleDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return UserRole::class;
    }
}
