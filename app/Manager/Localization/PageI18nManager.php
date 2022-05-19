<?php
declare(strict_types=1);

namespace App\Manager\Localization;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Localization\PageI18nDal;
use App\Manager\AbstractManager;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the entity persistence of page i18n data transfer objects.
 *
 * @package App\Manager\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PageI18nManager extends AbstractManager
{
    /**
     * @var PageI18nDal
     */
    private $pageI18nDal;
    
    /**
     * PageI18nManager constructor.
     *
     * @param PageI18nDal $pageI18nDal
     */
    public function __construct(PageI18nDal $pageI18nDal)
    {
        $this->pageI18nDal = $pageI18nDal;
    }
    
    /**
     * @param string $localeCode
     *
     * @return Collection
     */
    public function findAllByLocaleCode(string $localeCode): Collection
    {
        return $this->getRepository()->findAllByLocaleCode($localeCode);
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->pageI18nDal;
    }
    
    
}
