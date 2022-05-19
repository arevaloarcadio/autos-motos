<?php
declare(strict_types=1);

namespace App\DAL\Localization;

use App\DAL\AbstractEloquentDal;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the data access layer operations for translation.
 *
 * @package App\DAL\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TranslationDal extends AbstractEloquentDal
{
    /**
     * @param string $group
     * @param string $localeId
     *
     * @return Collection
     */
    public function findAllByGroupAndLocale(string $group, string $localeId): Collection
    {
        return $this->model->newQuery()->where('translation_key', 'LIKE', "$group.%")
                           ->where('locale_id', $localeId)
                           ->get();
    }
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Translation::class;
    }
    
}
