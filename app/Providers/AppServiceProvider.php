<?php

namespace App\Providers;

use App\Enum\Core\ApprovalStatusEnum;
use App\Models\AutoOption;
use App\Models\VehicleCategory;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\CarWheelDriveType;
use App\Models\Dealer;
use App\Models\Locale;
use App\Models\Translation;
use App\Models\Market;
use App\Models\User;
use App\Observers\Ad\VehicleCategoryObserver;
use App\Observers\CarAd\CarBodyTypeObserver;
use App\Observers\CarAd\CarFuelTypeObserver;
use App\Observers\CarAd\AutoOptionObserver;
use App\Observers\CarAd\CarTransmissionTypeObserver;
use App\Observers\CarAd\CarWheelDriveTypeObserver;
use App\Observers\Localization\LocaleObserver;
use App\Observers\Localization\TranslationObserver;
use App\Observers\Market\MarketObserver;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\AdTypeStorage;
use App\Service\Ad\Creator\AdCreatorOrchestrator;
use App\Service\Ad\Creator\AutoAdCreator;
use App\Service\Ad\Creator\MechanicAdCreator;
use App\Service\Ad\Creator\MobileHomeAdCreator;
use App\Service\Ad\Creator\MotoAdCreator;
use App\Service\Ad\Creator\RentalAdCreator;
use App\Service\Ad\Creator\ShopAdCreator;
use App\Service\Ad\Creator\TruckAdCreator;
use App\Service\Ad\Editor\AdEditorOrchestrator;
use App\Service\Ad\Editor\AutoAdEditor;
use App\Service\Ad\Editor\MechanicAdEditor;
use App\Service\Ad\Editor\MobileHomeAdEditor;
use App\Service\Ad\Editor\MotoAdEditor;
use App\Service\Ad\Editor\RentalAdEditor;
use App\Service\Ad\Editor\ShopAdEditor;
use App\Service\Ad\Finder\AdFinderOrchestrator;
use App\Service\Ad\Finder\AutoAdFinder;
use App\Service\Ad\Finder\MechanicAdFinder;
use App\Service\Ad\Finder\MobileHomeAdFinder;
use App\Service\Ad\Finder\MotoAdFinder;
use App\Service\Ad\Finder\RentalAdFinder;
use App\Service\Ad\Finder\ShopAdFinder;
use App\Service\Ad\Finder\TruckAdFinder;
use App\Service\Ad\UserFavouriteAdService;
use App\Service\Ad\Validator\AdValidationOrchestrator;
use App\Service\Ad\Validator\AutoAdValidator;
use App\Service\Ad\Validator\MechanicAdValidator;
use App\Service\Ad\Validator\MobileHomeAdValidator;
use App\Service\Ad\Validator\MotoAdValidator;
use App\Service\Ad\Validator\RentalAdValidator;
use App\Service\Ad\Validator\ShopAdValidator;
use App\Service\Ad\Validator\TruckAdValidator;
use App\Service\Ad\Viewer\AdViewerOrchestrator;
use App\Service\Ad\Viewer\AutoAdViewer;
use App\Service\Ad\Viewer\MechanicAdViewer;
use App\Service\Ad\Viewer\MobileHomeAdViewer;
use App\Service\Ad\Viewer\MotoAdViewer;
use App\Service\Ad\Viewer\RentalAdViewer;
use App\Service\Ad\Viewer\ShopAdViewer;
use App\Service\Ad\Viewer\TruckAdViewer;
use App\Service\Market\MarketStorage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            MarketStorage::class,
            function () {
                return new MarketStorage();
            }
        );

        $this->app->singleton(
            AdTypeStorage::class,
            function () {
                return new AdTypeStorage();
            }
        );

        $this->app->tag(
            [
                AutoAdFinder::class,
                MotoAdFinder::class,
                MobileHomeAdFinder::class,
                TruckAdFinder::class,
                MechanicAdFinder::class,
                RentalAdFinder::class,
                ShopAdFinder::class,
            ],
            ['ad_finder']
        );
        $this->app->singleton(
            AdFinderOrchestrator::class,
            function () {
                return new AdFinderOrchestrator(
                    $this->app->tagged('ad_finder'),
                    $this->app->get(UserFavouriteAdService::class)
                );
            }
        );

        $this->app->tag(
            [
                AutoAdCreator::class,
                MotoAdCreator::class,
                MobileHomeAdCreator::class,
                TruckAdCreator::class,
                MechanicAdCreator::class,
                RentalAdCreator::class,
                ShopAdCreator::class,
            ],
            ['ad_creator']
        );
        $this->app->singleton(
            AdCreatorOrchestrator::class,
            function () {
                return new AdCreatorOrchestrator(
                    $this->app->tagged('ad_creator'),
                    $this->app->get(AdCreateService::class)
                );
            }
        );

        $this->app->tag(
            [
                AutoAdEditor::class,
                MotoAdEditor::class,
                MobileHomeAdEditor::class,
                MechanicAdEditor::class,
                RentalAdEditor::class,
                ShopAdEditor::class,
            ],
            ['ad_editor']
        );
        $this->app->singleton(
            AdEditorOrchestrator::class,
            function () {
                return new AdEditorOrchestrator(
                    $this->app->tagged('ad_editor')
                );
            }
        );

        $this->app->tag(
            [
                AutoAdViewer::class,
                MotoAdViewer::class,
                MobileHomeAdViewer::class,
                TruckAdViewer::class,
                MechanicAdViewer::class,
                RentalAdViewer::class,
                ShopAdViewer::class,
            ],
            ['ad_viewer']
        );
        $this->app->singleton(
            AdViewerOrchestrator::class,
            function () {
                return new AdViewerOrchestrator(
                    $this->app->tagged('ad_viewer')
                );
            }
        );

        $this->app->tag(
            [
                AutoAdValidator::class,
                MotoAdValidator::class,
                MobileHomeAdValidator::class,
                TruckAdValidator::class,
                MechanicAdValidator::class,
                RentalAdValidator::class,
                ShopAdValidator::class,
            ],
            ['ad_validator']
        );
        $this->app->singleton(
            AdValidationOrchestrator::class,
            function () {
                return new AdValidationOrchestrator(
                    $this->app->tagged('ad_validator')
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //        DB::listen(
        //            function ($query) {
        //                Log::info(
        //                    $query->sql,
        //                    $query->bindings,
        //                    $query->time
        //                );
        //            }
        //        );
        $this->registerAppExtensions();
        $this->initialiseEloquentObservers();
    }

    /**
     * Register app extensions.
     */
    private function registerAppExtensions(): void
    {
        Blade::if(
            'userVerifiedAndDealerApproved',
            function () {
                /** @var User $user */
                $user         = auth()->user();
                $userVerified = $user->hasVerifiedEmail();
                if ($user instanceof User && $user->dealer instanceof Dealer) {
                    return $userVerified && $user->dealer->status === ApprovalStatusEnum::APPROVED;
                }

                return $userVerified;
            }
        );

        Blade::if(
            'dealerApproved',
            function () {
                /** @var User $user */
                $user = auth()->user();
                if ($user instanceof User && $user->dealer instanceof Dealer) {
                    return $user->dealer->status === ApprovalStatusEnum::APPROVED;
                }

                return true;
            }
        );

        Blade::if(
            'userVerified',
            function () {
                return optional(auth()->user())->hasVerifiedEmail();
            }
        );
    }

    /**
     * Initialise eloquent observers.
     */
    private function initialiseEloquentObservers(): void
    {
        CarBodyType::observe(CarBodyTypeObserver::class);
        CarFuelType::observe(CarFuelTypeObserver::class);
        CarTransmissionType::observe(CarTransmissionTypeObserver::class);
        CarWheelDriveType::observe(CarWheelDriveTypeObserver::class);
        AutoOption::observe(AutoOptionObserver::class);
        VehicleCategory::observe(VehicleCategoryObserver::class);
        Market::observe(MarketObserver::class);
        Locale::observe(LocaleObserver::class);

        //Translation::observe(TranslationObserver::class);
    }
}
