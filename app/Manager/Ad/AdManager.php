<?php

declare(strict_types=1);

namespace App\Manager\Ad;

use App\DAL\Ad\AdDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Models\Ad;

/**
 * Defines the entity persistence of ad data transfer objects.
 *
 * @package App\Manager\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdManager extends AbstractManager
{
    /**
     * @var AdDal
     */
    private $adDal;

    /**
     * @param AdDal $adDal
     */
    public function __construct(AdDal $adDal)
    {
        $this->adDal = $adDal;
    }

    /**
     * @param Ad  $instance
     * @param int $newStatus
     *
     * @return Ad
     */
    public function changeStatus(Ad $instance, int $newStatus): Ad
    {
        $instance->status = $newStatus;
        $instance->save();

        return $instance;
    }

    public function changeSeller(Ad $ad, array $input): Ad
    {
        $ad->user_id = $input['user_id'];
        $ad->save();

        $specificAd = $ad->getSpecificAd();
        if (null === $specificAd || false === method_exists($specificAd, 'dealer')) {
            return $ad;
        }

        $specificAd->dealer_id = $input['dealer_id'];
        $specificAd->save();

        return $ad;
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->adDal;
    }
}
