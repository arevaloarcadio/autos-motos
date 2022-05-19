<?php
declare(strict_types=1);

namespace App\DAL\Localization;


use App\DAL\AbstractEloquentDal;
use App\Models\PageI18n;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the data access layer operations for page i18n.
 *
 * @package App\DAL\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PageI18nDal extends AbstractEloquentDal
{
    /**
     * @param string $localeCode
     *
     * @return Collection
     */
    public function findAllByLocaleCode(string $localeCode): Collection
    {
        return $this->model->newQuery()
                           ->select(['pages_i18n.title', 'pages_i18n.slug', 'pages_i18n.page_id'])
                           ->with(['page'])
                           ->join('locales as l', 'pages_i18n.locale_id', '=', 'l.id')
                           ->where('l.code', '=', $localeCode)
                           ->orderBy('pages_i18n.title', 'ASC')
                           ->get();
    }

    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return PageI18n::class;
    }
}
