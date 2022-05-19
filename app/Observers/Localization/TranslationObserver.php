<?php
declare(strict_types=1);

namespace App\Observers\Localization;

use App\Manager\Localization\LocaleManager;
use App\Models\Localization\Translation;
use Illuminate\Support\Facades\Cache;

/**
 * @package App\Observers\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TranslationObserver
{
    /**
     * @var LocaleManager
     */
    private $localeManager;
    
    /**
     * TranslationObserver constructor.
     *
     * @param LocaleManager $localeManager
     */
    public function __construct(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }
    
    
    /**
     * Handle the translation "created" event.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function created(Translation $translation)
    {
        //
    }
    
    /**
     * Handle the translation "updated" event.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function updated(Translation $translation)
    {
        $group = explode('.', $translation->translation_key)[0];
        
        Cache::forget($this->localeManager->getCacheKey($translation->locale->code, $group));
    }
    
    /**
     * Handle the translation "deleted" event.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function deleted(Translation $translation)
    {
        //
    }
    
    /**
     * Handle the translation "restored" event.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function restored(Translation $translation)
    {
        //
    }
    
    /**
     * Handle the translation "force deleted" event.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function forceDeleted(Translation $translation)
    {
        //
    }
}
