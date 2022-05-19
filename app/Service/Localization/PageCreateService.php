<?php
declare(strict_types=1);

namespace App\Service\Localization;

use App\Manager\Localization\LocaleManager;
use App\Manager\Localization\PageI18nManager;
use App\Manager\Localization\PageManager;
use App\Models\Page;
use App\Models\PageI18n;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Defines the business logic associated with page creation.
 *
 * @package App\Service\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PageCreateService
{
    /**
     * @var PageManager
     */
    private $pageManager;
    
    /**
     * @var PageI18nManager
     */
    private $pageI18nManager;
    
    /**
     * @var LocaleManager
     */
    private $localeManager;
    
    /**
     * PageCreateService constructor.
     *
     * @param PageManager     $pageManager
     * @param PageI18nManager $pageI18nManager
     * @param LocaleManager   $localeManager
     */
    public function __construct(
        PageManager $pageManager,
        PageI18nManager $pageI18nManager,
        LocaleManager $localeManager
    ) {
        $this->pageManager     = $pageManager;
        $this->pageI18nManager = $pageI18nManager;
        $this->localeManager   = $localeManager;
    }
    
    /**
     * @param array $data
     *
     * @return Page
     */
    public function create(array $data): Page
    {
        $pageTitle    = $data['internal_name'];
        $internalName = Str::slug($pageTitle, '_');
        /** @var Page $page */
        $page = $this->pageManager->create(['internal_name' => $internalName]);
        
        $localeCode = App::getLocale();
        $locale     = $this->localeManager->findOneBy(['code' => $localeCode]);
        
        $pageTranslation        = new PageI18n();
        $pageTranslation->title = $pageTitle;
        $pageTranslation->locale()->associate($locale);
        $pageTranslation->page()->associate($page);
        
        $this->pageI18nManager->save($pageTranslation);
        
        return $page;
    }
}
