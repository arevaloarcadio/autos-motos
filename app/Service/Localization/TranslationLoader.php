<?php
declare(strict_types=1);

namespace App\Service\Localization;

use App\Manager\Localization\LocaleManager;
use App\Manager\Localization\TranslationManager;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\FileLoader;

/**
 * Loads the translations from the database
 *
 * @package App\Service\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TranslationLoader extends FileLoader
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
     * TranslationLoader constructor.
     *
     * @param LocaleManager      $localeManager
     * @param TranslationManager $translationManager
     * @param Filesystem         $files
     * @param                    $path
     */
    public function __construct(
        LocaleManager $localeManager,
        TranslationManager $translationManager,
        Filesystem $files,
        $path
    ) {
        parent::__construct($files, $path);
        $this->translationManager = $translationManager;
        $this->localeManager      = $localeManager;
    }
    
    /**
     * @param string $locale
     * @param string $group
     * @param null   $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        $translations = $this->loadTranslationsByLocaleCodeAndGroup($locale, $group);
        if (count($translations) > 0) {
            return $translations;
        }
        
        //        $defaultLocale = Config::get('app.fallback_locale');
        //        if ($locale !== $defaultLocale) {
        //            return $this->loadTranslationsByLocaleCode($defaultLocale);
        //        }
        
        return [];
    }
    
    /**
     * @param string $locale
     *
     * @param string $group
     *
     * @return array
     */
    private function loadTranslationsByLocaleCodeAndGroup(string $locale, string $group): array
    {
        return Cache::rememberForever(
            $this->localeManager->getCacheKey($locale, $group),
            function () use ($locale, $group) {
                $localeModel = $this->localeManager->findOneBy(
                    [
                        'code' => $locale,
                    ]
                );
                $output      = [];
                
                if ($localeModel instanceof Locale) {
                    $translations = $this->translationManager->findAllByGroupAndLocaleId($group, $localeModel->id);
                    /** @var Translation $translation */
                    foreach ($translations as $translation) {
                        $key                                   = preg_replace(
                            "/{$group}\\./",
                            '',
                            $translation->translation_key,
                            1
                        );
                        $output[$key] = $translation->translation_value;
                    }
                }
                
                return $output;
            }
        );
    }
}
