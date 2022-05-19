<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarModelDal;
use App\DAL\DataAccessLayerInterface;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the entity persistence of car model data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarModelManager extends AbstractManager
{
    /**
     * @var CarModelDal
     */
    private $carModelDal;
    
    /**
     * CarModelManager constructor.
     *
     * @param CarModelDal $carModelDal
     */
    public function __construct(CarModelDal $carModelDal)
    {
        $this->carModelDal = $carModelDal;
    }
    
    /**
     * @param string $makeId
     * @param int    $itemsPerPage
     *
     * @return LengthAwarePaginator
     */
    public function findAllByMakeId(
        string $makeId,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE
    ): LengthAwarePaginator {
        return $this->getRepository()->findAllByMakeId($makeId, $itemsPerPage);
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carModelDal;
    }
}
