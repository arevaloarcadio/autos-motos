<?php
declare(strict_types=1);

namespace App\DAL\User;

use App\DAL\AbstractEloquentDal;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the data access layer operations for user.
 *
 * @package App\DAL\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserDal extends AbstractEloquentDal
{
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return User::class;
    }
    
    /**
     * @param string $dealerId
     * @param int    $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return LengthAwarePaginator
     */
    public function findAllByDealerId(
        string $dealerId,
        int $itemsPerPage,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC'
    ): LengthAwarePaginator {
        return $this->model->newQuery()
                           ->select(['users.*', 'dealer_user.dealer_id'])
                           ->join('dealer_user', 'users.id', '=', 'dealer_user.user_id')
                           ->where('dealer_user.dealer_id', '=', $dealerId)
                           ->orderBy($orderBy, $orderDirection)
                           ->paginate($itemsPerPage);
    }
}
