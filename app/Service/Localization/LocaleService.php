<?php
declare(strict_types=1);

namespace App\Service\Localization;

use App\Manager\Localization\LocaleManager;
use App\Models\Locale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * @package App\Service\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class LocaleService
{
    /**
     * @var LocaleManager
     */
    private $localeManager;
    
    /**
     * @var string
     */
    private $fallbackLocale;
    
    /**
     * LocaleService constructor.
     *
     * @param LocaleManager $localeManager
     */
    public function __construct(LocaleManager $localeManager)
    {
        $this->localeManager  = $localeManager;
        $this->fallbackLocale = config('app.fallback_locale');
    }
    
    /**
     * @param string|null $localeCode
     *
     * @return Locale
     */
    public function changeLocaleByCode(?string $localeCode = null): Locale
    {
        $locale = $this->findLocaleByCode($localeCode);
        
        Session::put('locale', $locale->code);
        
        return $locale;
    }
    
    /**
     * @param string|null $localeCode
     *
     * @return Locale
     */
    private function findLocaleByCode(?string $localeCode = null): Locale
    {
        if (null === $localeCode) {
            $localeCode = $this->fallbackLocale;
        }
        /** @var Locale $locale */
        $locale = $this->localeManager->findOneBy(['code' => $localeCode]);
        if ($locale instanceof Locale) {
            return $locale;
        }
        
        $locale = $this->localeManager->findOneBy(['code' => $this->fallbackLocale]);
        
        return $locale;
    }
}
