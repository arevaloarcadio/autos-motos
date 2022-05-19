<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarSpecDal;
use App\DAL\DataAccessLayerInterface;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use App\Models\CarSpec;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the entity persistence of car spec data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarSpecManager extends AbstractManager
{
    /**
     * @var CarSpecDal
     */
    private $carSpecDal;
    
    /**
     * CarSpecManager constructor.
     *
     * @param CarSpecDal $carSpecDal
     */
    public function __construct(CarSpecDal $carSpecDal)
    {
        $this->carSpecDal = $carSpecDal;
    }
    
    /**
     * @param array $input
     *
     * @return Collection
     */
    public function search(array $input): Collection
    {
        return $this->carSpecDal->search($input);
    }
    
    /**
     * @param array $input
     *
     * @return int[]
     */
    public function retrieveYearsByModelId(array $input): array
    {
        $results = $this->carSpecDal->retrieveYearsByModelId($input);
        
        $years = [];
        /** @var CarSpec $result */
        foreach ($results as $result) {
            if (null === $result->production_start_year) {
                continue;
            }
            if (null === $result->production_end_year) {
                $years[$result->production_start_year] = $result->production_start_year;
                continue;
            }
            for ($i = $result->production_start_year; $i <= $result->production_end_year; $i++) {
                $years[$i] = $i;
            }
        }
        $uniqueYears = array_unique(array_values($years));
        rsort($uniqueYears);
        
        return $uniqueYears;
    }
    
    /**
     * @param string $generationId
     * @param int    $itemsPerPage
     *
     * @return LengthAwarePaginator
     */
    public function findAllByGenerationId(
        string $generationId,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE
    ): LengthAwarePaginator {
        return $this->getRepository()->findAllByGenerationId($generationId, $itemsPerPage);
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carSpecDal;
    }
}
