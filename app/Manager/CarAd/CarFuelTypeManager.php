<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarFuelTypeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;
use App\Models\CarFuelType;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of car fuel type data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class CarFuelTypeManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var CarFuelTypeDal
     */
    private $carFuelTypeDal;

    /**
     * CarFuelTypeManager constructor.
     *
     * @param CarFuelTypeDal $carFuelTypeDal
     */
    public function __construct(CarFuelTypeDal $carFuelTypeDal)
    {
        $this->carFuelTypeDal = $carFuelTypeDal;
    }

    /**
     * @param array $input
     *
     * @return Collection
     */
    public function search(array $input): Collection
    {
        return $this->carFuelTypeDal->search($input);
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carFuelTypeDal;
    }
}
