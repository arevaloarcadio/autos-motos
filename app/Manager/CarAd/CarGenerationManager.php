<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarGenerationDal;
use App\DAL\DataAccessLayerInterface;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the entity persistence of car generation data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarGenerationManager extends AbstractManager
{
    /**
     * @var CarGenerationDal
     */
    private $carGenerationDal;
    
    /**
     * CarBodyTypeManager constructor.
     *
     * @param CarGenerationDal $carGenerationDal
     */
    public function __construct(CarGenerationDal $carGenerationDal)
    {
        $this->carGenerationDal = $carGenerationDal;
    }
    
    /**
     * @param string $modelId
     * @param int    $itemsPerPage
     * @param array  $loadRelationships
     *
     * @return LengthAwarePaginator
     */
    public function findAllByModelId(
        string $modelId,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        array $loadRelationships = []
    ): LengthAwarePaginator {
        return $this->getRepository()->findAllByModelId($modelId, $itemsPerPage, $loadRelationships);
    }
    
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carGenerationDal;
    }
}
