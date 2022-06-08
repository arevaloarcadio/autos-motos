<?php
declare(strict_types=1);

namespace App\Observers;

use App\Manager\Localization\LocaleManager;
use App\Manager\Localization\TranslationManager;
use App\Models\ILocalizable;
use App\Models\Localization\Locale;
use App\Models\Localization\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use ReflectionException;

/**
 * Observer superclass for models that should manage translations.
 *
 * @package App\Observers
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
abstract class AbstractLocalizableModelObserver
{
    /**
     * @var LocaleManager
     */
    private $localeManager;
    
    /**
     * @var TranslationManager
     */
    private $translationManager;
    
    /**
     * @var Translator
     */
    private $translator;
    
    /**
     * AdCategoryObserver constructor.
     *
     * @param LocaleManager      $localeManager
     * @param TranslationManager $translationManager
     * @param Translator         $translator
     */
    public function __construct(
        LocaleManager $localeManager,
        TranslationManager $translationManager,
        Translator $translator
    ) {
        $this->localeManager      = $localeManager;
        $this->translationManager = $translationManager;
        $this->translator         = $translator;
    }
    
    /**
     * Handle the ad category "created" event.
     *
     * @param ILocalizable $model
     *
     * @return void
     * @throws ReflectionException
     */
    public function created($model)
    {
        /*$locales = $this->localeManager->findAllPaginated();
        

        foreach ($locales as $locale) {
            $translationKey = sprintf('%s.%s', $this->getGroupName($model), $model->internal_name);
            if ($this->translator->has($translationKey, $locale->code, false)) {
                continue;
            }
            $translation = new Translation();
            $translation->locale()->associate($locale);
            $translation->translation_key   = $translationKey;
            $translation->translation_value = $model->internal_name;
            $translation->resource_id       = $model->id;
            $this->translationManager->save($translation);
            $this->clearTranslationsCache($model, $locale->code);
        }*/
        
    }
    
    /**
     * Handle the ad category "updated" event.
     *
     * @param ILocalizable $model
     *
     * @return void
     * @throws ReflectionException
     */
    public function updated(ILocalizable $model)
    {
        $translations = $this->translationManager->findBy(
            [
                'resource_id' => $model->id,
            ]
        );
        
        foreach ($translations as $translation) {
            $this->translationManager->delete($translation);
        }
        
        $this->created($model);
    }
    
    /**
     * Handle the ad category "deleted" event.
     *
     * @param ILocalizable $model
     *
     * @return void
     * @throws ReflectionException
     */
    public function deleted(ILocalizable $model)
    {
        $translations = $this->findAllTranslationsByResourceId($model->id);
        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $this->clearTranslationsCache($model, $translation->locale->code);
            $this->translationManager->delete($translation);
        }
    }
    
    /**
     * @param string $resourceId
     *
     * @return Collection
     */
    private function findAllTranslationsByResourceId(string $resourceId): Collection
    {
        return $this->translationManager->findBy(
            [
                'resource_id' => $resourceId,
            ]
        );
    }
    
    /**
     * @param ILocalizable $model
     *
     * @return string
     * @throws ReflectionException
     */
    private function getGroupName(ILocalizable $model): string
    {
        $className = (new \ReflectionClass($model))->getShortName();
        
        return Str::snake($className);
    }
    
    /**
     * @param ILocalizable $model
     * @param string       $localeCode
     *
     * @throws ReflectionException
     */
    private function clearTranslationsCache(ILocalizable $model, string $localeCode): void
    {
        Cache::forget(sprintf('translations_%s_%s', $this->getGroupName($model), $localeCode));
    }
}
