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

/* Auto-generated admin routes */
Route::get('/payments/cancelled', 'App\Http\Controllers\PaypalController@cancelled')->name('cancelled');
Route::get('/payments/approval', 'App\Http\Controllers\PaypalController@approval')->name('approval');

Route::namespace('App\Http\Controllers')->group(static function() {
    Route::post('/marks','MarkController@store')->name('marks.store');
    Route::post('/models','ModelController@store')->name('models.store');
    Route::post('/data','ModelController@store')->name('models.store');
});
Route::get('/data', function () {
    return view('data');
});

Route::namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
    Route::get('/confirm/email/{email}','UsersController@confirm_email')->name('confirm_email');
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
        Route::get('/confirm/email','UserController@confirm_email')->name('confirm_email');

    });
    
});








/* Auto-generated admin routes */


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('reviews')->name('reviews/')->group(static function() {
            Route::get('/',                                             'ReviewsController@index')->name('index');
            Route::get('/create',                                       'ReviewsController@create')->name('create');
            Route::post('/',                                            'ReviewsController@store')->name('store');
            Route::get('/{review}/edit',                                'ReviewsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ReviewsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{review}',                                    'ReviewsController@update')->name('update');
            Route::delete('/{review}',                                  'ReviewsController@destroy')->name('destroy');
        });
    });
});



/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('payment-histories')->name('payment-histories/')->group(static function() {
            Route::get('/',                                             'PaymentHistoriesController@index')->name('index');
            Route::get('/create',                                       'PaymentHistoriesController@create')->name('create');
            Route::post('/',                                            'PaymentHistoriesController@store')->name('store');
            Route::get('/{paymentHistory}/edit',                        'PaymentHistoriesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'PaymentHistoriesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{paymentHistory}',                            'PaymentHistoriesController@update')->name('update');
            Route::delete('/{paymentHistory}',                          'PaymentHistoriesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('ad-user-plans')->name('ad-user-plans/')->group(static function() {
            Route::get('/',                                             'AdUserPlansController@index')->name('index');
            Route::get('/create',                                       'AdUserPlansController@create')->name('create');
            Route::post('/',                                            'AdUserPlansController@store')->name('store');
            Route::get('/{adUserPlan}/edit',                            'AdUserPlansController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdUserPlansController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adUserPlan}',                                'AdUserPlansController@update')->name('update');
            Route::delete('/{adUserPlan}',                              'AdUserPlansController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('characteristic-plans')->name('characteristic-plans/')->group(static function() {
            Route::get('/',                                             'CharacteristicPlansController@index')->name('index');
            Route::get('/create',                                       'CharacteristicPlansController@create')->name('create');
            Route::post('/',                                            'CharacteristicPlansController@store')->name('store');
            Route::get('/{characteristicPlan}/edit',                    'CharacteristicPlansController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CharacteristicPlansController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{characteristicPlan}',                        'CharacteristicPlansController@update')->name('update');
            Route::delete('/{characteristicPlan}',                      'CharacteristicPlansController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('characteristic-promotion-plans')->name('characteristic-promotion-plans/')->group(static function() {
            Route::get('/',                                             'CharacteristicPromotionPlansController@index')->name('index');
            Route::get('/create',                                       'CharacteristicPromotionPlansController@create')->name('create');
            Route::post('/',                                            'CharacteristicPromotionPlansController@store')->name('store');
            Route::get('/{characteristicPromotionPlan}/edit',           'CharacteristicPromotionPlansController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CharacteristicPromotionPlansController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{characteristicPromotionPlan}',               'CharacteristicPromotionPlansController@update')->name('update');
            Route::delete('/{characteristicPromotionPlan}',             'CharacteristicPromotionPlansController@destroy')->name('destroy');
        });
    });
});