<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::namespace('App\Http\Controllers')->group(static function() {
    Route::prefix('admin')->group(static function() {
        Route::post('/login', 'UserController@authenticate_admin');
    });
});

Route::group(['middleware' => ['jwt.verify']], function() {
    
    Route::post('/recovery-password-admin', 'App\Http\Controllers\UserController@recovery_password_admin');

    Route::namespace('App\Http\Controllers')->group(static function() {
        Route::post('/billings', 'BillingController@store')->name('billing-store');
        Route::post('/paypal-payments', 'PaypalController@pay')->name('paypal-payment');
        Route::post('/paypal-payments-anuncio', 'PaypalController@payAnuncio')->name('paypal-payment.anuncio');
        Route::post('/stripe-payments', 'StripeController@pay')->name('stripe-payment');
    });

    Route::namespace('App\Http\Controllers\Admin')->group(static function() {

        Route::prefix('admin')->group(static function() {
            Route::post('/import/massive', 'ImportController@import_massive');
        });

        Route::prefix('ads')->name('ads/')->group(static function() {
            Route::get('/byUser',                                   'AdsController@byUser')->name('byUser');
            Route::get('/promoted-simple/byUser',                   'AdsController@getPromotedSimpleAdsByUser')->name('getPromotedSimpleAdsByUser');
            Route::get('/promoted-front-page/byUser',               'AdsController@getPromotedFrontPageAdsByUser')->name('getPromotedFrontPageAdsByUser');
        });

        Route::prefix('reviews')->name('reviews/')->group(static function() {
            Route::get('/byUser',                                   'ReviewsController@byUser')->name('byUser');
        });


        Route::prefix('users/')->name('dealers/')->group(static function() {
            Route::get('/dealer',                                   'DealersController@show')->name('show');
        });

        Route::prefix('auto-ads')->name('auto-ads/')->group(static function() {
            Route::post('/',                                            'AutoAdsController@store')->name('store');
            Route::post('/{id}',                                        'AutoAdsController@update')->name('store');
        });

        Route::prefix('moto-ads')->name('moto-ads/')->group(static function() {
            Route::post('/',                                            'MotoAdsController@store')->name('store');
             Route::post('/{id}',                                        'MotoAdsController@update')->name('store');
        });

        Route::prefix('mobile-home-ads')->name('mobile-home-ads/')->group(static function() {
            Route::post('/',                                            'MobileHomeAdsController@store')->name('store');
            Route::post('/{id}',                                            'MobileHomeAdsController@update')->name('store');
        });

        Route::prefix('truck-ads')->name('truck-ads/')->group(static function() {
            Route::post('/',                                            'TruckAdsController@store')->name('store');
            Route::post('/{id}',                                            'TruckAdsController@update')->name('store');
        });

        Route::prefix('shop-ads')->name('mobile-home-ads/')->group(static function() {
            Route::post('/',                                            'ShopAdsController@store')->name('store');
            Route::post('/{id}',                                            'ShopAdsController@update')->name('store');
        });
        
        Route::prefix('mechanic-ads')->name('mechanic-ads/')->group(static function() {
            Route::post('/',                                          'MechanicAdsController@store')->name('store');
            Route::post('/{id}',                                       'MechanicAdsController@update')->name('store');

        });  

        Route::prefix('rental-ads')->name('rental-ads/')->group(static function() {
            Route::post('/',                                           'RentalAdsController@store')->name('store');
            Route::post('/{id}',                                        'RentalAdsController@update')->name('store');
        });

        Route::prefix('dealers')->name('dealers/')->group(static function() {
            Route::post('/{dealer}',                                    'DealersController@update')->name('update');
        });

        Route::prefix('dealer-show-rooms')->name('dealer-show-rooms/')->group(static function() {
            Route::get('/{dealer_id}/dealer',                           'DealerShowRoomsController@byDealerId')->name('byDealerId');
            //Route::get('/{dealer_id}/dealer',                           'DealerShowRoomsController@byDealerId')->name('byDealerId');
            Route::post('/{dealerShowRoom}',                            'DealerShowRoomsController@update')->name('update');
        });

        Route::prefix('users')->name('users/')->group(static function() {
            Route::post('/ocassional',                                  'UsersController@updateOcassional')->name('update');
            Route::post('/update_profile',                              'UsersController@updateProfile')->name('update_profile');
            Route::get('/dealer',                                       'UsersController@getDealer')->name('show');
            Route::get('/info',                                         'UsersController@getUserInfo')->name('show');
          
        });
        
        Route::prefix('receipts')->name('receipts/')->group(static function() {
            Route::get('/',                                             'ReceiptsController@index')->name('index');
            Route::post('/',                                            'ReceiptsController@store')->name('store');
            //Route::post('/bulk-destroy',                                'ReceiptsController@bulkDestroy')->name('bulk-destroy');
            //Route::post('/{receipt}',                                   'ReceiptsController@update')->name('update');
            //Route::delete('/{receipt}',                                 'ReceiptsController@destroy')->name('destroy');
        });

        Route::prefix('plans')->name('plans/')->group(static function() {
            Route::get('/',                                             'PlansController@index')->name('index');
            Route::get('/byUser',                                       'PlansController@byUser')->name('index');
            Route::get('/{plan}',                                       'PlansController@show')->name('show');
            Route::post('/',                                            'PlansController@store')->name('store');
            Route::post('/{plan}',                                      'PlansController@update')->name('update');
            Route::delete('/{plan}',                                    'PlansController@destroy')->name('destroy');
        }); 

        Route::prefix('payment-histories')->name('payment-histories/')->group(static function() {
            Route::get('/',                                             'PaymentHistoriesController@index')->name('index');
            Route::get('/byUser',                                       'PaymentHistoriesController@byUser')->name('index');
            Route::get('/{user}',                                       'PaymentHistoriesController@byUserAdmin')->name('index');
            Route::post('/',                                            'PaymentHistoriesController@store')->name('store');
            //Route::post('/{paymentHistory}',                            'PaymentHistoriesController@update')->name('update');
        });

        Route::prefix('ad-user-plans')->name('ad-user-plans/')->group(static function() {
            Route::get('/',                                             'AdUserPlansController@index')->name('index');
            Route::post('/',                                            'AdUserPlansController@store')->name('store');
            Route::post('/{adUserPlan}',                                'AdUserPlansController@update')->name('update');
            Route::delete('/{adUserPlan}',                              'AdUserPlansController@destroy')->name('destroy');
        });

        Route::prefix('promoted-ads')->name('promoted-ads/')->group(static function() {
            Route::post('/',                                            'PromotedAdsController@store')->name('store');
            Route::get('/info',                                         'PromotedAdsController@getInfoPromoted')->name('store');
            Route::delete('/{ad_id}',                                   'PromotedAdsController@deletePromoted')->name('store');
        });
    });
});

Route::namespace('App\Http\Controllers\Admin')->group(static function() {
    Route::prefix('admin')->group(static function() {
        Route::get('/download_csv', 'ImportController@downloadCsv');
    });
});

Route::post('/login', 'App\Http\Controllers\UserController@authenticate');

Route::post('/recovery-password-email', 'App\Http\Controllers\UserController@recovery_email');
Route::post('/recovery-password-code', 'App\Http\Controllers\UserController@recovery_code');
Route::post('/recovery-password', 'App\Http\Controllers\UserController@recovery_password');

Route::namespace('App\Http\Controllers\Admin')->group(static function() {
   

    Route::prefix('auto-ads')->name('auto-ads/')->group(static function() {
		Route::get('/',                                             'AutoAdsController@index')->name('index');
	});
	
	Route::prefix('vehicle-categories')->name('vehicle-categories/')->group(static function() {
		Route::get('/',                                             'VehicleCategoriesController@index')->name('index');
	});
	
	Route::prefix('car-fuel-types')->name('car-fuel-types/')->group(static function() {
		Route::get('/',                                             'CarFuelTypesController@index')->name('index');
	});

	Route::prefix('car-transmission-types')->name('car-transmission-types/')->group(static function() {
	 	Route::get('/',                                             'CarTransmissionTypesController@index')->name('index');
	});

	

/* Auto-generated admin routes */

        Route::prefix('ads')->name('ads/')->group(static function() {
            Route::get('/',                                             'AdsController@index')->name('index');

            Route::post('/filter',                                      'AdsController@index')->name('filter');

            Route::get('/byDealer/{dealer_id}',                                       'AdsController@byDealer')->name('byDealer');
            Route::get('/bySource',                                     'AdsController@bySource')->name('bySource');
            Route::get('/countToday',                                   'AdsController@countAdsToday')->name('countAdsToday');
            Route::get('/countImportToday',                             'AdsController@countAdsImportToday')->name('countAdsImportToday');
            Route::get('/byCsv/{csv_ad_id}',                            'AdsController@byCsv')->name('byCsv');
            Route::get('/groupByCsv',                                   'AdsController@groupByCsv')->name('groupByCsv');
            //Route::get('/create',                                       'AdsController@create')->name('create');
            Route::get('/{ad_id}',                                      'AdsController@show')->name('show');
            Route::post('/',                                            'AdsController@store')->name('store');
            Route::post('/search_advanced',                             'AdsController@searchAdvanced')->name('searchAdvanced');
            Route::post('/count_search_advanced',                       'AdsController@countSearchAdvanced')->name('searchAdvanced');
            
            Route::post('/search_advanced_mechanic',                    'AdsController@searchAdvancedMechanic')->name('searchAdvancedMechanic');
            
            Route::post('/search_ads_like',                             'AdsController@searchAdsLike')->name('search_ads_like');

            Route::post('/search_ads_like_title',                       'AdsController@searchAdsLikeTitle')->name('search_ads_like');

         
            Route::post('/{ad_id}/rejected_comment',                    'AdsController@storeCommentRejected')->name('storeCommentRejected');
            Route::post('/rejected_comment_individual_ads',             'AdsController@storeCommentsRejectedIndividual')->name('storeCommentRejected');
            Route::post('/{csv_ad_id}/ads_rejected_comment',            'AdsController@storeCommentsRejected')->name('ads_rejected_comment');
            Route::post('/{status}/approved_rejected',                  'AdsController@setApprovedRejected')->name('store');
            Route::post('/{status}/approved_rejected_individual',       'AdsController@setApprovedRejectedIndividual')->name('store');
            Route::post('/{status}/approved_rejected_ads',              'AdsController@setApprovedRejected')->name('store');
            
            //Route::get('/{ad}/edit',                                    'AdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{ad}',                                        'AdsController@update')->name('update');
            Route::delete('/{ad}',                                      'AdsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('ad-images')->name('ad-images/')->group(static function() {
            Route::get('/',                                             'AdImagesController@index')->name('index');
            //Route::get('/create',                                       'AdImagesController@create')->name('create');
            Route::post('/',                                            'AdImagesController@store')->name('store');
            //Route::get('/{adImage}/edit',                               'AdImagesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdImagesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adImage}',                                   'AdImagesController@update')->name('update');
            Route::delete('/{adImage}',                                 'AdImagesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('ad-image-versions')->name('ad-image-versions/')->group(static function() {
            Route::get('/',                                             'AdImageVersionsController@index')->name('index');
            //Route::get('/create',                                       'AdImageVersionsController@create')->name('create');
            Route::post('/',                                            'AdImageVersionsController@store')->name('store');
            //Route::get('/{adImageVersion}/edit',                        'AdImageVersionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdImageVersionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adImageVersion}',                            'AdImageVersionsController@update')->name('update');
            Route::delete('/{adImageVersion}',                          'AdImageVersionsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('ad-makes')->name('ad-makes/')->group(static function() {
            Route::get('/',                                             'AdMakesController@index')->name('index');
            //Route::get('/create',                                       'AdMakesController@create')->name('create');
            Route::post('/',                                            'AdMakesController@store')->name('store');
          //  Route::get('/{adMake}/edit',                                'AdMakesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdMakesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adMake}',                                    'AdMakesController@update')->name('update');
            Route::delete('/{adMake}',                                  'AdMakesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('ad-models')->name('ad-models/')->group(static function() {
            Route::get('/',                                             'AdModelsController@index')->name('index');
            //Route::get('/create',                                       'AdModelsController@create')->name('create');
            Route::post('/',                                            'AdModelsController@store')->name('store');
            //Route::get('/{adModel}/edit',                               'AdModelsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdModelsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adModel}',                                   'AdModelsController@update')->name('update');
            Route::delete('/{adModel}',                                 'AdModelsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('auto-ads')->name('auto-ads/')->group(static function() {
            Route::get('/',                                             'AutoAdsController@index')->name('index');
            Route::get('/promoted',                                     'AutoAdsController@autoAdsPromotedFrontPage')->name('autoAdsPromotedFrontPage');
            //Route::get('/create',                                       'AutoAdsController@create')->name('create');
            Route::post('/search/like',                                   'AutoAdsController@searchLike');
            
            //Route::get('/{autoAd}/edit',                                'AutoAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AutoAdsController@bulkDestroy')->name('bulk-destroy');
           
            Route::delete('/{autoAd}',                                  'AutoAdsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('auto-ad-options')->name('auto-ad-options/')->group(static function() {
            Route::get('/',                                             'AutoAdOptionsController@index')->name('index');
            //Route::get('/create',                                       'AutoAdOptionsController@create')->name('create');
            Route::post('/',                                            'AutoAdOptionsController@store')->name('store');
            //Route::get('/{autoAdOption}/edit',                          'AutoAdOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AutoAdOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{autoAdOption}',                              'AutoAdOptionsController@update')->name('update');
            Route::delete('/{autoAdOption}',                            'AutoAdOptionsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('auto-options')->name('auto-options/')->group(static function() {
            Route::get('/',                                             'AutoOptionsController@index')->name('index');
            //Route::get('/create',                                       'AutoOptionsController@create')->name('create');
            Route::post('/',                                            'AutoOptionsController@store')->name('store');
            //Route::get('/{autoOption}/edit',                            'AutoOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AutoOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{autoOption}',                                'AutoOptionsController@update')->name('update');
            Route::delete('/{autoOption}',                              'AutoOptionsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('banners')->name('banners/')->group(static function() {
            Route::get('/',                                             'BannersController@index')->name('index');
           // Route::get('/create',                                       'BannersController@create')->name('create');
            Route::post('/',                                            'BannersController@store')->name('store');
           // Route::get('/{banner}/edit',                                'BannersController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'BannersController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{banner}',                                    'BannersController@update')->name('update');
            Route::delete('/{banner}',                                  'BannersController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-body-types')->name('car-body-types/')->group(static function() {
            Route::get('/',                                             'CarBodyTypesController@index')->name('index');
            //Route::get('/create',                                       'CarBodyTypesController@create')->name('create');
            Route::post('/',                                            'CarBodyTypesController@store')->name('store');
           // Route::get('/{carBodyType}/edit',                           'CarBodyTypesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarBodyTypesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carBodyType}',                               'CarBodyTypesController@update')->name('update');
            Route::delete('/{carBodyType}',                             'CarBodyTypesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-fuel-types')->name('car-fuel-types/')->group(static function() {
            Route::get('/',                                             'CarFuelTypesController@index')->name('index');
            //Route::get('/create',                                       'CarFuelTypesController@create')->name('create');
            Route::post('/',                                            'CarFuelTypesController@store')->name('store');
            //Route::get('/{carFuelType}/edit',                           'CarFuelTypesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarFuelTypesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carFuelType}',                               'CarFuelTypesController@update')->name('update');
            Route::delete('/{carFuelType}',                             'CarFuelTypesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-generations')->name('car-generations/')->group(static function() {
            Route::get('/',                                             'CarGenerationsController@index')->name('index');
            //Route::get('/create',                                       'CarGenerationsController@create')->name('create');
            Route::post('/',                                            'CarGenerationsController@store')->name('store');
            //Route::get('/{carGeneration}/edit',                         'CarGenerationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarGenerationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carGeneration}',                             'CarGenerationsController@update')->name('update');
            Route::delete('/{carGeneration}',                           'CarGenerationsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-makes')->name('car-makes/')->group(static function() {
            Route::get('/',                                             'CarMakesController@index')->name('index');
            //Route::get('/create',                                       'CarMakesController@create')->name('create');
            Route::post('/',                                            'CarMakesController@store')->name('store');
            //Route::get('/{carMake}/edit',                               'CarMakesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarMakesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carMake}',                                   'CarMakesController@update')->name('update');
            Route::delete('/{carMake}',                                 'CarMakesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-models')->name('car-models/')->group(static function() {
            Route::get('/',                                             'CarModelsController@index')->name('index');
            //Route::get('/create',                                       'CarModelsController@create')->name('create');
            Route::post('/',                                            'CarModelsController@store')->name('store');
            //Route::get('/{carModel}/edit',                              'CarModelsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarModelsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carModel}',                                  'CarModelsController@update')->name('update');
            Route::delete('/{carModel}',                                'CarModelsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('car-specs')->name('car-specs/')->group(static function() {
            Route::get('/',                                             'CarSpecsController@index')->name('index');
            //Route::get('/create',                                       'CarSpecsController@create')->name('create');
            Route::post('/',                                            'CarSpecsController@store')->name('store');
            //Route::get('/{carSpec}/edit',                               'CarSpecsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarSpecsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carSpec}',                                   'CarSpecsController@update')->name('update');
            Route::delete('/{carSpec}',                                 'CarSpecsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('car-transmission-types')->name('car-transmission-types/')->group(static function() {
            Route::get('/',                                             'CarTransmissionTypesController@index')->name('index');
            //Route::get('/create',                                       'CarTransmissionTypesController@create')->name('create');
            Route::post('/',                                            'CarTransmissionTypesController@store')->name('store');
            //Route::get('/{carTransmissionType}/edit',                   'CarTransmissionTypesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarTransmissionTypesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carTransmissionType}',                       'CarTransmissionTypesController@update')->name('update');
            Route::delete('/{carTransmissionType}',                     'CarTransmissionTypesController@destroy')->name('destroy');
        });
/* Auto-generated admin routes */

        Route::prefix('car-wheel-drive-types')->name('car-wheel-drive-types/')->group(static function() {
            Route::get('/',                                             'CarWheelDriveTypesController@index')->name('index');
            //Route::get('/create',                                       'CarWheelDriveTypesController@create')->name('create');
            Route::post('/',                                            'CarWheelDriveTypesController@store')->name('store');
            //Route::get('/{carWheelDriveType}/edit',                     'CarWheelDriveTypesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CarWheelDriveTypesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{carWheelDriveType}',                         'CarWheelDriveTypesController@update')->name('update');
            Route::delete('/{carWheelDriveType}',                       'CarWheelDriveTypesController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('dealers')->name('dealers/')->group(static function() {
            Route::get('/',                                             'DealersController@index')->name('index');
            //Route::get('/create',                                       'DealersController@create')->name('create');
            Route::post('/',                                            'DealersController@store')->name('store');
            //Route::get('/{dealer}/edit',                                'DealersController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DealersController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{dealer}',                                  'DealersController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('dealer-show-rooms')->name('dealer-show-rooms/')->group(static function() {
            Route::get('/',                                             'DealerShowRoomsController@index')->name('index');
           
            //Route::get('/create',                                       'DealerShowRoomsController@create')->name('create');
            Route::post('/',                                            'DealerShowRoomsController@store')->name('store');
            //Route::get('/{dealerShowRoom}/edit',                        'DealerShowRoomsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DealerShowRoomsController@bulkDestroy')->name('bulk-destroy');
           
            Route::delete('/{dealerShowRoom}',                          'DealerShowRoomsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('equipment')->name('equipment/')->group(static function() {
            Route::get('/',                                             'EquipmentController@index')->name('index');
            //Route::get('/create',                                       'EquipmentController@create')->name('create');
            Route::post('/',                                            'EquipmentController@store')->name('store');
            //Route::get('/{equipment}/edit',                             'EquipmentController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'EquipmentController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{equipment}',                                 'EquipmentController@update')->name('update');
            Route::delete('/{equipment}',                               'EquipmentController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('equipment-options')->name('equipment-options/')->group(static function() {
            Route::get('/',                                             'EquipmentOptionsController@index')->name('index');
            //Route::get('/create',                                       'EquipmentOptionsController@create')->name('create');
            Route::post('/',                                            'EquipmentOptionsController@store')->name('store');
           // Route::get('/{equipmentOption}/edit',                       'EquipmentOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'EquipmentOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{equipmentOption}',                           'EquipmentOptionsController@update')->name('update');
            Route::delete('/{equipmentOption}',                         'EquipmentOptionsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('generations')->name('generations/')->group(static function() {
            Route::get('/',                                             'GenerationsController@index')->name('index');
            //Route::get('/create',                                       'GenerationsController@create')->name('create');
            Route::post('/',                                            'GenerationsController@store')->name('store');
            //Route::get('/{generation}/edit',                            'GenerationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'GenerationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{generation}',                                'GenerationsController@update')->name('update');
            Route::delete('/{generation}',                              'GenerationsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('locales')->name('locales/')->group(static function() {
            Route::get('/',                                             'LocalesController@index')->name('index');
            //Route::get('/create',                                       'LocalesController@create')->name('create');
            Route::post('/',                                            'LocalesController@store')->name('store');
            //Route::get('/{locale}/edit',                                'LocalesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LocalesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{locale}',                                    'LocalesController@update')->name('update');
            Route::delete('/{locale}',                                  'LocalesController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('makes')->name('makes/')->group(static function() {
            Route::get('/',                                             'MakesController@index')->name('index');
            //Route::get('/create',                                       'MakesController@create')->name('create');
            Route::post('/',                                            'MakesController@store')->name('store');
            //Route::get('/{make}/edit',                                  'MakesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MakesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{make}',                                      'MakesController@update')->name('update');
            Route::delete('/{make}',                                    'MakesController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('markets')->name('markets/')->group(static function() {
            Route::get('/',                                             'MarketsController@index')->name('index');
            //Route::get('/create',                                       'MarketsController@create')->name('create');
            Route::post('/',                                            'MarketsController@store')->name('store');
            //Route::get('/{market}/edit',                                'MarketsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MarketsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{market}',                                    'MarketsController@update')->name('update');
            Route::delete('/{market}',                                  'MarketsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('mechanic-ads')->name('mechanic-ads/')->group(static function() {
            Route::get('/',                                             'MechanicAdsController@index')->name('index');
            Route::get('/promoted',                                     'MechanicAdsController@mechanicAdsPromotedFrontPage')->name('mechanicAdsPromotedFrontPage');
            Route::post('/search/like',                                   'MechanicAdsController@searchLike');
            
            //Route::get('/create',                                       'MechanicAdsController@create')->name('create');
            //Route::get('/{mechanicAd}/edit',                            'MechanicAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MechanicAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{mechanicAd}',                              'MechanicAdsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('models')->name('models/')->group(static function() {
            Route::get('/',                                             'ModelsController@index')->name('index');
            //Route::get('/create',                                       'ModelsController@create')->name('create');
            Route::post('/',                                            'ModelsController@store')->name('store');
            //Route::get('/{model}/edit',                                 'ModelsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ModelsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{model}',                                     'ModelsController@update')->name('update');
            Route::delete('/{model}',                                   'ModelsController@destroy')->name('destroy');
        });

        Route::prefix('moto-ad-options')->name('moto-ad-options/')->group(static function() {
            Route::get('/',                                             'MotoAdOptionsController@index')->name('index');
            //Route::get('/create',                                       'MotoAdOptionsController@create')->name('create');
            Route::post('/',                                            'MotoAdOptionsController@store')->name('store');
            //Route::get('/{motoAdOption}/edit',                          'MotoAdOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MotoAdOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{motoAdOption}',                              'MotoAdOptionsController@update')->name('update');
            Route::delete('/{motoAdOption}',                            'MotoAdOptionsController@destroy')->name('destroy');
        });
        Route::prefix('mobile-home-ads')->name('mobile-home-ads/')->group(static function() {
            Route::get('/',                                             'MobileHomeAdsController@index')->name('index');
            Route::get('/promoted',                                     'MobileHomeAdsController@mobileHomeAdsPromotedFrontPage')->name('autoAdsPromotedFrontPage');
            //Route::get('/create',                                       'MobileHomeAdsController@create')->name('create');
            
            Route::post('/search/like',                                   'MobileHomeAdsController@searchLike');

            //Route::get('/{mobileHomeAd}/edit',                          'MobileHomeAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MobileHomeAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{mobileHomeAd}',                            'MobileHomeAdsController@destroy')->name('destroy');
        });

        Route::prefix('mobile-home-ad-options')->name('mobile-home-ad-options/')->group(static function() {
            Route::get('/',                                             'MobileHomeAdOptionsController@index')->name('index');
            //Route::get('/create',                                       'MobileHomeAdOptionsController@create')->name('create');
            Route::post('/',                                            'MobileHomeAdOptionsController@store')->name('store');
            //Route::get('/{mobileHomeAdOption}/edit',                    'MobileHomeAdOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MobileHomeAdOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{mobileHomeAdOption}',                        'MobileHomeAdOptionsController@update')->name('update');
            Route::delete('/{mobileHomeAdOption}',                      'MobileHomeAdOptionsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('moto-ads')->name('moto-ads/')->group(static function() {
            Route::get('/',                                             'MotoAdsController@index')->name('index');
            Route::get('/promoted',                                     'MotoAdsController@motoAdsPromotedFrontPage')->name('motoAdsPromotedFrontPage');
            //Route::get('/create',                                       'MotoAdsController@create')->name('create');
           
            Route::post('/search/like',                                   'MotoAdsController@searchLike');
            //Route::get('/{motoAd}/edit',                                'MotoAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MotoAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{motoAd}',                                  'MotoAdsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('operations')->name('operations/')->group(static function() {
            Route::get('/',                                             'OperationsController@index')->name('index');
            //Route::get('/create',                                       'OperationsController@create')->name('create');
            Route::post('/',                                            'OperationsController@store')->name('store');
            //Route::get('/{operation}/edit',                             'OperationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'OperationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{operation}',                                 'OperationsController@update')->name('update');
            Route::delete('/{operation}',                               'OperationsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('options')->name('options/')->group(static function() {
            Route::get('/',                                             'OptionsController@index')->name('index');
            //Route::get('/create',                                       'OptionsController@create')->name('create');
            Route::post('/',                                            'OptionsController@store')->name('store');
            //Route::get('/{option}/edit',                                'OptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'OptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{option}',                                    'OptionsController@update')->name('update');
            Route::delete('/{option}',                                  'OptionsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('rental-ads')->name('rental-ads/')->group(static function() {
            Route::get('/',                                             'RentalAdsController@index')->name('index');
            Route::get('/promoted',                                     'RentalAdsController@rentalAdsPromotedFrontPage')->name('motoAdsPromotedFrontPage');
            Route::post('/search/like',                                   'RentalAdsController@searchLike');
            
            //Route::get('/create',                                       'RentalAdsController@create')->name('create');
            //Route::get('/{rentalAd}/edit',                              'RentalAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'RentalAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{rentalAd}',                                'RentalAdsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('roles')->name('roles/')->group(static function() {
            Route::get('/',                                             'RolesController@index')->name('index');
            //Route::get('/create',                                       'RolesController@create')->name('create');
            Route::post('/',                                            'RolesController@store')->name('store');
            //Route::get('/{role}/edit',                                  'RolesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'RolesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{role}',                                      'RolesController@update')->name('update');
            Route::delete('/{role}',                                    'RolesController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('series')->name('series/')->group(static function() {
            Route::get('/',                                             'SeriesController@index')->name('index');
            //Route::get('/create',                                       'SeriesController@create')->name('create');
            Route::post('/',                                            'SeriesController@store')->name('store');
            //Route::get('/{series}/edit',                                'SeriesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SeriesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{series}',                                    'SeriesController@update')->name('update');
            Route::delete('/{series}',                                  'SeriesController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('shop-ads')->name('shop-ads/')->group(static function() {
            Route::get('/',                                             'ShopAdsController@index')->name('index');
            Route::get('/promoted',                                     'ShopAdsController@shopAdsPromotedFrontPage')->name('shopAdsPromotedFrontPage');
            //Route::get('/create',                                       'ShopAdsController@create')->name('create');
            Route::post('/search_advanced',                             'ShopAdsController@search_advanced')->name('store');
            Route::post('/search/like',                                   'ShopAdsController@searchLike');

            //Route::get('/{shopAd}/edit',                                'ShopAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ShopAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{shopAd}',                                  'ShopAdsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('specifications')->name('specifications/')->group(static function() {
            Route::get('/',                                             'SpecificationsController@index')->name('index');
            //Route::get('/create',                                       'SpecificationsController@create')->name('create');
            Route::post('/',                                            'SpecificationsController@store')->name('store');
            //Route::get('/{specification}/edit',                         'SpecificationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SpecificationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{specification}',                             'SpecificationsController@update')->name('update');
            Route::delete('/{specification}',                           'SpecificationsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('translations')->name('translations/')->group(static function() {
            Route::get('/',                                             'TranslationsController@index')->name('index');
            //Route::get('/create',                                       'TranslationsController@create')->name('create');
            Route::post('/',                                            'TranslationsController@store')->name('store');
            //Route::get('/{translation}/edit',                           'TranslationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TranslationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{translation}',                               'TranslationsController@update')->name('update');
            Route::delete('/{translation}',                             'TranslationsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('trims')->name('trims/')->group(static function() {
            Route::get('/',                                             'TrimsController@index')->name('index');
            //Route::get('/create',                                       'TrimsController@create')->name('create');
            Route::post('/',                                            'TrimsController@store')->name('store');
            //Route::get('/{trim}/edit',                                  'TrimsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TrimsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{trim}',                                      'TrimsController@update')->name('update');
            Route::delete('/{trim}',                                    'TrimsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('trim-specifications')->name('trim-specifications/')->group(static function() {
            Route::get('/',                                             'TrimSpecificationsController@index')->name('index');
            //Route::get('/create',                                       'TrimSpecificationsController@create')->name('create');
            Route::post('/',                                            'TrimSpecificationsController@store')->name('store');
            //Route::get('/{trimSpecification}/edit',                     'TrimSpecificationsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TrimSpecificationsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{trimSpecification}',                         'TrimSpecificationsController@update')->name('update');
            Route::delete('/{trimSpecification}',                       'TrimSpecificationsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('truck-ads')->name('truck-ads/')->group(static function() {
            Route::get('/',                                             'TruckAdsController@index')->name('index');
            Route::get('/promoted',                                     'TruckAdsController@truckAdsPromotedFrontPage')->name('truckAdsPromotedFrontPage');
            //Route::get('/create',                                       'TruckAdsController@create')->name('create');
            
            Route::post('/search/like',                                   'TruckAdsController@searchLike');

            //Route::get('/{truckAd}/edit',                               'TruckAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TruckAdsController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{truckAd}',                                 'TruckAdsController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('users')->name('users/')->group(static function() {
            Route::get('/',                                             'UsersController@index')->name('index');
          
            Route::get('/{user}',                                       'UsersController@show')->name('show');
            Route::get('/{user}/info_ads',                              'UsersController@countAdsByUser')->name('show');
          
            //Route::get('/create',                                       'UsersController@create')->name('create');
            Route::post('/',                                            'UsersController@store')->name('store');
            Route::post('/{user}/status',                               'UsersController@setStatus')->name('setStatus');
            Route::post('/{user}',                                      'UsersController@update')->name('update');
            //Route::get('/{user}/edit',                                  'UsersController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UsersController@bulkDestroy')->name('bulk-destroy');
            Route::delete('/{user}',                                    'UsersController@destroy')->name('destroy');
        });

/* Auto-generated admin routes */

        Route::prefix('users-favourite-ads')->name('users-favourite-ads/')->group(static function() {
            Route::get('/',                                             'UsersFavouriteAdsController@index')->name('index');
            //Route::get('/create',                                       'UsersFavouriteAdsController@create')->name('create');
            Route::post('/',                                            'UsersFavouriteAdsController@store')->name('store');
            //Route::get('/{usersFavouriteAd}/edit',                      'UsersFavouriteAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UsersFavouriteAdsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{usersFavouriteAd}',                          'UsersFavouriteAdsController@update')->name('update');
            Route::delete('/{usersFavouriteAd}',                        'UsersFavouriteAdsController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('users-favourite-ad-searches')->name('users-favourite-ad-searches/')->group(static function() {
            Route::get('/',                                             'UsersFavouriteAdSearchesController@index')->name('index');
            //Route::get('/create',                                       'UsersFavouriteAdSearchesController@create')->name('create');
            Route::post('/',                                            'UsersFavouriteAdSearchesController@store')->name('store');
            //Route::get('/{usersFavouriteAdSearch}/edit',                'UsersFavouriteAdSearchesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UsersFavouriteAdSearchesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{usersFavouriteAdSearch}',                    'UsersFavouriteAdSearchesController@update')->name('update');
            Route::delete('/{usersFavouriteAdSearch}',                  'UsersFavouriteAdSearchesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('user-roles')->name('user-roles/')->group(static function() {
            Route::get('/',                                             'UserRolesController@index')->name('index');
            //Route::get('/create',                                       'UserRolesController@create')->name('create');
            Route::post('/',                                            'UserRolesController@store')->name('store');
            //Route::get('/{userRole}/edit',                              'UserRolesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UserRolesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{userRole}',                                  'UserRolesController@update')->name('update');
            Route::delete('/{userRole}',                                'UserRolesController@destroy')->name('destroy');
        });


/* Auto-generated admin routes */

        Route::prefix('vehicle-categories')->name('vehicle-categories/')->group(static function() {
            Route::get('/',                                             'VehicleCategoriesController@index')->name('index');
            Route::get('/category',                                             'VehicleCategoriesController@indexCategory')->name('indexCategory');
            //Route::get('/create',                                       'VehicleCategoriesController@create')->name('create');
            Route::post('/',                                            'VehicleCategoriesController@store')->name('store');
            //Route::get('/{vehicleCategory}/edit',                       'VehicleCategoriesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'VehicleCategoriesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{vehicleCategory}',                           'VehicleCategoriesController@update')->name('update');
            Route::delete('/{vehicleCategory}',                         'VehicleCategoriesController@destroy')->name('destroy');
        });

        Route::prefix('characteristics')->name('characteristics/')->group(static function() {
            Route::get('/',                                             'CharacteristicsController@index')->name('index');
            //Route::get('/create',                                       'CharacteristicsController@create')->name('create');
            Route::post('/',                                            'CharacteristicsController@store')->name('store');
            //Route::get('/{characteristic}/edit',                        'CharacteristicsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CharacteristicsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{characteristic}',                            'CharacteristicsController@update')->name('update');
            Route::delete('/{characteristic}',                          'CharacteristicsController@destroy')->name('destroy');
        });

        Route::prefix('sub-characteristics')->name('sub-characteristics/')->group(static function() {
            Route::get('/',                                             'SubCharacteristicsController@index')->name('index');
            //Route::get('/create',                                       'SubCharacteristicsController@create')->name('create');
            Route::post('/',                                            'SubCharacteristicsController@store')->name('store');
            //Route::get('/{subCharacteristic}/edit',                     'SubCharacteristicsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SubCharacteristicsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{subCharacteristic}',                         'SubCharacteristicsController@update')->name('update');
            Route::delete('/{subCharacteristic}',                       'SubCharacteristicsController@destroy')->name('destroy');
        });

        Route::prefix('ad-sub-characteristics')->name('ad-sub-characteristics/')->group(static function() {
            Route::get('/',                                             'AdSubCharacteristicsController@index')->name('index');
            //Route::get('/create',                                       'AdSubCharacteristicsController@create')->name('create');
            Route::post('/',                                            'AdSubCharacteristicsController@store')->name('store');
            //Route::get('/{adSubCharacteristic}/edit',                   'AdSubCharacteristicsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdSubCharacteristicsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adSubCharacteristic}',                       'AdSubCharacteristicsController@update')->name('update');
            Route::delete('/{adSubCharacteristic}',                     'AdSubCharacteristicsController@destroy')->name('destroy');
        });

        Route::prefix('reviews')->name('reviews/')->group(static function() {
            Route::get('/',                                             'ReviewsController@index')->name('index');
           
            Route::post('/',                                            'ReviewsController@store')->name('store');
            Route::post('/bulk-destroy',                                'ReviewsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{review}',                                    'ReviewsController@update')->name('update');
            Route::delete('/{review}',                                  'ReviewsController@destroy')->name('destroy');
        });
});

/*
Route::group(['middleware' => ['jwt.verify']], function() {
	Route::namespace('App\Http\Controllers\Admin')->group(static function() {
	
		Route::prefix('vehicle-categories')->name('vehicle-categories/')->group(static function() {
	        Route::get('/create',                                       'VehicleCategoriesController@create')->name('create');
	        Route::post('/',                                            'VehicleCategoriesController@store')->name('store');
	        Route::get('/{vehicleCategory}/edit',                       'VehicleCategoriesController@edit')->name('edit');
	        Route::post('/bulk-destroy',                                'VehicleCategoriesController@bulkDestroy')->name('bulk-destroy');
	        Route::post('/{vehicleCategory}',                           'VehicleCategoriesController@update')->name('update');
	        Route::delete('/{vehicleCategory}',                         'VehicleCategoriesController@destroy')->name('destroy');
	    });

		Route::prefix('brands')->name('brands/')->group(static function() {
		    Route::get('/',                                             'BrandsController@index')->name('index');
		    Route::get('/create',                                       'BrandsController@create')->name('create');
		    Route::post('/',                                            'BrandsController@store')->name('store');
		    Route::get('/{brand}/edit',                                 'BrandsController@edit')->name('edit');
		    Route::post('/bulk-destroy',                                'BrandsController@bulkDestroy')->name('bulk-destroy');
		    Route::post('/{brand}',                                     'BrandsController@update')->name('update');
		    Route::delete('/{brand}',                                   'BrandsController@destroy')->name('destroy');
		});

		Route::prefix('categories')->name('categories/')->group(static function() {
		    Route::get('/',                                             'CategoriesController@index')->name('index');
		    Route::get('/create',                                       'CategoriesController@create')->name('create');
		    Route::post('/',                                            'CategoriesController@store')->name('store');
		    Route::get('/{category}/edit',                              'CategoriesController@edit')->name('edit');
		    Route::post('/bulk-destroy',                                'CategoriesController@bulkDestroy')->name('bulk-destroy');
		    Route::post('/{category}',                                  'CategoriesController@update')->name('update');
		    Route::delete('/{category}',                                'CategoriesController@destroy')->name('destroy');
		});

	    Route::prefix('attributes')->name('attributes/')->group(static function() {
	        Route::get('/',                                             'AttributesController@index')->name('index');
	        Route::get('/create',                                       'AttributesController@create')->name('create');
	        Route::post('/',                                            'AttributesController@store')->name('store');
	        Route::get('/{attribute}/edit',                             'AttributesController@edit')->name('edit');
	        Route::post('/bulk-destroy',                                'AttributesController@bulkDestroy')->name('bulk-destroy');
	        Route::post('/{attribute}',                                 'AttributesController@update')->name('update');
	        Route::delete('/{attribute}',                               'AttributesController@destroy')->name('destroy');
	    });

		Route::prefix('attribute-values')->name('attribute-values/')->group(static function() {
		    Route::get('/',                                             'AttributeValuesController@index')->name('index');
		    Route::get('/create',                                       'AttributeValuesController@create')->name('create');
		    Route::post('/',                                            'AttributeValuesController@store')->name('store');
		    Route::get('/{attributeValue}/edit',                        'AttributeValuesController@edit')->name('edit');
		    Route::post('/bulk-destroy',                                'AttributeValuesController@bulkDestroy')->name('bulk-destroy');
		    Route::post('/{attributeValue}',                            'AttributeValuesController@update')->name('update');
		    Route::delete('/{attributeValue}',                          'AttributeValuesController@destroy')->name('destroy');
		});

		Route::prefix('stores')->group(static function() {
            Route::get('/',                                             'StoresController@index')->name('index');
            Route::post('/',                                            'StoresController@store');
            Route::post('/bulk-destroy',                                'StoresController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{store}',                                     'StoresController@update')->name('update');
            Route::delete('/{store}',                                   'StoresController@destroy')->name('destroy');
        });       

        Route::prefix('companies')->name('companies/')->group(static function() {
            Route::get('/',                                             'CompaniesController@index')->name('index');
            Route::get('/create',                                       'CompaniesController@create')->name('create');
            Route::post('/',                                            'CompaniesController@store')->name('store');
            Route::get('/{company}/edit',                               'CompaniesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CompaniesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{company}',                                   'CompaniesController@update')->name('update');
            Route::delete('/{company}',                                 'CompaniesController@destroy')->name('destroy');
        });

        Route::prefix('auto-ads')->name('auto-ads/')->group(static function() {
            Route::get('/create',                                       'AutoAdsController@create')->name('create');
            Route::post('/',                                            'AutoAdsController@store')->name('store');
            Route::get('/{autoAd}/edit',                                'AutoAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AutoAdsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{autoAd}',                                    'AutoAdsController@update')->name('update');
            Route::delete('/{autoAd}',                                  'AutoAdsController@destroy')->name('destroy');
        });


	    Route::prefix('car-body-types')->name('car-body-types/')->group(static function() {
	        Route::get('/',                                             'CarBodyTypesController@index')->name('index');
	        Route::get('/create',                                       'CarBodyTypesController@create')->name('create');
	        Route::post('/',                                            'CarBodyTypesController@store')->name('store');
	        Route::get('/{carBodyType}/edit',                           'CarBodyTypesController@edit')->name('edit');
	        Route::post('/bulk-destroy',                                'CarBodyTypesController@bulkDestroy')->name('bulk-destroy');
	        Route::post('/{carBodyType}',                               'CarBodyTypesController@update')->name('update');
	        Route::delete('/{carBodyType}',                             'CarBodyTypesController@destroy')->name('destroy');
	    });
			

			
	    Route::prefix('car-fuel-types')->name('car-fuel-types/')->group(static function() {
	        Route::get('/create',                                       'CarFuelTypesController@create')->name('create');
	        Route::post('/',                                            'CarFuelTypesController@store')->name('store');
	        Route::get('/{carFuelType}/edit',                           'CarFuelTypesController@edit')->name('edit');
	        Route::post('/bulk-destroy',                                'CarFuelTypesController@bulkDestroy')->name('bulk-destroy');
	        Route::post('/{carFuelType}',                               'CarFuelTypesController@update')->name('update');
	        Route::delete('/{carFuelType}',                             'CarFuelTypesController@destroy')->name('destroy');
	    });
		

	
		Route::prefix('car-transmission-types')->name('car-transmission-types/')->group(static function() {
	        Route::get('/create',                                       'CarTransmissionTypesController@create')->name('create');
	        Route::post('/',                                            'CarTransmissionTypesController@store')->name('store');
	        Route::get('/{carTransmissionType}/edit',                   'CarTransmissionTypesController@edit')->name('edit');
	        Route::post('/bulk-destroy',                                'CarTransmissionTypesController@bulkDestroy')->name('bulk-destroy');
	        Route::post('/{carTransmissionType}',                       'CarTransmissionTypesController@update')->name('update');
	        Route::delete('/{carTransmissionType}',                     'CarTransmissionTypesController@destroy')->name('destroy');
	    });
	});
});*/


