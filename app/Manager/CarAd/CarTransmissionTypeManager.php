<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarTransmissionTypeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the entity persistence of car transmission type data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarTransmissionTypeManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var CarTransmissionTypeDal
     */
    private $carTransmissionTypeDal;
    
    /**
     * CarTransmissionTypeManager constructor.
     *
     * @param CarTransmissionTypeDal $carTransmissionTypeDal
     */
    public function __construct(CarTransmissionTypeDal $carTransmissionTypeDal)
    {
        $this->carTransmissionTypeDal = $carTransmissionTypeDal;
    }
    
    /**
     * @param array $input
     *
     * @return Collection
     */
    public function search(array $input): Collection
    {
        return $this->carTransmissionTypeDal->search($input);
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carTransmissionTypeDal;
    }
}
