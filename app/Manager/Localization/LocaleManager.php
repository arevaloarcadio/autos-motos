<?php
declare(strict_types=1);

namespace App\Manager\Localization;


use App\DAL\DataAccessLayerInterface;
use App\DAL\Localization\LocaleDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of locale data transfer objects.
 *
 * @package App\Manager\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class LocaleManager extends AbstractManager
{
    /**
     * @var LocaleDal
     */
    private $localeDal;
    
    /**
     * LocaleManager constructor.
     *
     * @param LocaleDal $localeDal
     */
    public function __construct(LocaleDal $localeDal)
    {
        $this->localeDal = $localeDal;
    }
    
    /**
     * @param string $localeCode
     * @param string $group
     *
     * @return string
     */
    public function getCacheKey(string $localeCode, string $group): string
    {
        return sprintf('translations_%s_%s', strtolower($group), strtolower($localeCode));
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->localeDal;
    }
}
