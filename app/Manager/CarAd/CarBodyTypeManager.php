<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarBodyTypeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the entity persistence of car body type data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarBodyTypeManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var CarBodyTypeDal
     */
    private $carBodyTypeDal;

    /**
     * CarBodyTypeManager constructor.
     *
     * @param CarBodyTypeDal $carBodyTypeDal
     */
    public function __construct(CarBodyTypeDal $carBodyTypeDal)
    {
        $this->carBodyTypeDal = $carBodyTypeDal;
    }

    /**
     * @param array $input
     *
     * @return Collection
     */
    public function search(array $input): Collection
    {
        return $this->carBodyTypeDal->search($input);
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carBodyTypeDal;
    }
}
