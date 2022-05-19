<?php
declare(strict_types=1);

namespace App\Manager\User;

use App\DAL\DataAccessLayerInterface;
use App\DAL\User\UserDal;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the entity persistence of user data transfer objects.
 *
 * @package App\Manager\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserManager extends AbstractManager
{
    /**
     * @var UserDal
     */
    private $userDal;
    
    /**
     * UserManager constructor.
     *
     * @param UserDal $userDal
     */
    public function __construct(UserDal $userDal)
    {
        $this->userDal = $userDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->userDal;
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
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC'
    ): LengthAwarePaginator {
        return $this->userDal->findAllByDealerId($dealerId, $itemsPerPage, $orderBy, $orderDirection);
    }
}
