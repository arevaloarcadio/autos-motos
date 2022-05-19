<?php

declare(strict_types=1);

namespace App\Manager\Content;

use App\DAL\Content\BannerDal;
use App\DAL\DataAccessLayerInterface;
use App\DAL\Operation\OperationDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of banner data transfer objects.
 *
 * @package App\Manager\Content
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class BannerManager extends AbstractManager
{
    /**
     * @var BannerDal
     */
    private $bannerDal;

    public function __construct(BannerDal $bannerDal)
    {
        $this->bannerDal = $bannerDal;
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->bannerDal;
    }
}
