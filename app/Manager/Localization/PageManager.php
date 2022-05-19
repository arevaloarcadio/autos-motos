<?php
declare(strict_types=1);

namespace App\Manager\Localization;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Localization\PageDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of page data transfer objects.
 *
 * @package App\Manager\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PageManager extends AbstractManager
{
    /**
     * @var PageDal
     */
    private $pageDal;
    
    /**
     * PageManager constructor.
     *
     * @param PageDal $pageDal
     */
    public function __construct(PageDal $pageDal)
    {
        $this->pageDal = $pageDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->pageDal;
    }
}
