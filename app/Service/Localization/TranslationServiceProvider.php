<?php
declare(strict_types=1);

namespace App\Service\Localization;

use App\Manager\Localization\LocaleManager;
use App\Manager\Localization\TranslationManager;
use Illuminate\Translation\TranslationServiceProvider as ServiceProvider;
/**
 * Class TranslationServiceProvider.
 *
 * @package App\Service\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TranslationServiceProvider extends ServiceProvider
{
    public function registerLoader()
    {
        $this->app->singleton(
            'translation.loader',
            function ($app) {
                $translationManager = $this->app->get(TranslationManager::class);
                $localeManager      = $this->app->get(LocaleManager::class);
                
                return new TranslationLoader($localeManager, $translationManager, $app['files'], $app['path.lang']);
            }
        );
    }
}
