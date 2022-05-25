<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::namespace('App\Http\Controllers')->group(static function() {
    Route::post('/marks','MarkController@store')->name('marks.store');
    Route::post('/models','ModelController@store')->name('models.store');
     Route::post('/data','ModelController@store')->name('models.store');
});
Route::get('/data', function () {
    return view('data');
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users')->name('admin-users/')->group(static function() {
            Route::get('/',                                             'AdminUsersController@index')->name('index');
            Route::get('/create',                                       'AdminUsersController@create')->name('create');
            Route::post('/',                                            'AdminUsersController@store')->name('store');
            Route::get('/{adminUser}/impersonal-login',                 'AdminUsersController@impersonalLogin')->name('impersonal-login');
            Route::get('/{adminUser}/edit',                             'AdminUsersController@edit')->name('edit');
            Route::post('/{adminUser}',                                 'AdminUsersController@update')->name('update');
            Route::delete('/{adminUser}',                               'AdminUsersController@destroy')->name('destroy');
            Route::get('/{adminUser}/resend-activation',                'AdminUsersController@resendActivationEmail')->name('resendActivationEmail');
        });
    });
});




/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('moto-ad-options')->name('moto-ad-options/')->group(static function() {
            Route::get('/',                                             'MotoAdOptionsController@index')->name('index');
            Route::get('/create',                                       'MotoAdOptionsController@create')->name('create');
            Route::post('/',                                            'MotoAdOptionsController@store')->name('store');
            Route::get('/{motoAdOption}/edit',                          'MotoAdOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MotoAdOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{motoAdOption}',                              'MotoAdOptionsController@update')->name('update');
            Route::delete('/{motoAdOption}',                            'MotoAdOptionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('mobile-home-ads')->name('mobile-home-ads/')->group(static function() {
            Route::get('/',                                             'MobileHomeAdsController@index')->name('index');
            Route::get('/create',                                       'MobileHomeAdsController@create')->name('create');
            Route::post('/',                                            'MobileHomeAdsController@store')->name('store');
            Route::get('/{mobileHomeAd}/edit',                          'MobileHomeAdsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MobileHomeAdsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{mobileHomeAd}',                              'MobileHomeAdsController@update')->name('update');
            Route::delete('/{mobileHomeAd}',                            'MobileHomeAdsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('mobile-home-ad-options')->name('mobile-home-ad-options/')->group(static function() {
            Route::get('/',                                             'MobileHomeAdOptionsController@index')->name('index');
            Route::get('/create',                                       'MobileHomeAdOptionsController@create')->name('create');
            Route::post('/',                                            'MobileHomeAdOptionsController@store')->name('store');
            Route::get('/{mobileHomeAdOption}/edit',                    'MobileHomeAdOptionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MobileHomeAdOptionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{mobileHomeAdOption}',                        'MobileHomeAdOptionsController@update')->name('update');
            Route::delete('/{mobileHomeAdOption}',                      'MobileHomeAdOptionsController@destroy')->name('destroy');
        });
    });
});