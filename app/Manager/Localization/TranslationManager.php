<?php
declare(strict_types=1);

namespace App\Manager\Localization;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Localization\TranslationDal;
use App\Manager\AbstractManager;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the entity persistence of translation data transfer objects.
 *
 * @package App\Manager\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TranslationManager extends AbstractManager
{
    /**
     * @var TranslationDal
     */
    private $translationDal;
    
    /**
     * TranslationManager constructor.
     *
     * @param TranslationDal $translationDal
     */
    public function __construct(TranslationDal $translationDal)
    {
        $this->translationDal = $translationDal;
    }
    
    /**
     * @param string $group
     * @param string $localeId
     *
     * @return Collection
     */
    public function findAllByGroupAndLocaleId(string $group, string $localeId): Collection
    {
        return $this->translationDal->findAllByGroupAndLocale($group, $localeId);
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->translationDal;
    }
}
