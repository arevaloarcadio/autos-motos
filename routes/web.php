<?php

use Illuminate\Support\Facades\Route;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

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


Route::get('/clear/redis', function () {
    $clear = Redis::flushDB();
    return response()->json(['data' => 'OK','redis' => $clear ]);
});


Route::get('/aprobado', function () {
    $plan = Plan::find('212157f7-fdc9-4442-b6a9-c7a60fb27e3c');
    $user = User::find('0041c53b-1044-4824-a8e8-99f1bac630b7');

    return view('landing.aprobado')->with('plan',$plan)->with('user',$user);
});
//https://automotos.dattatech.com/seller/perfil

//https://automotos.dattatech.com/seller/tienda
Route::get('/cancelado', function () {
    return view('landing.cancelado');
});

/* Auto-generated admin routes */
Route::get('/payments/cancelled', 'App\Http\Controllers\PaypalController@cancelled')->name('cancelled');
Route::get('/payments/approval', 'App\Http\Controllers\PaypalController@approval')->name('approval');
Route::get('/stripe-payments/approval', 'App\Http\Controllers\StripeController@approval')->name('stripe-approval');
Route::get('/stripe-payments/cancelled', 'App\Http\Controllers\StripeController@cancelled')->name('stripe-cancelled');

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

    });

});


Route::prefix('admin')->namespace('App\Http\Controllers')->name('admin/')->group(static function() {
    Route::get('/confirm/email','UserController@confirm_email')->name('confirm_email');
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


