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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'App\Http\Controllers\UserController@register');
Route::post('/login', 'App\Http\Controllers\UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
	Route::namespace('App\Http\Controllers\Admin')->group(static function() {
	
		Route::prefix('vehicle-categories')->name('vehicle-categories/')->group(static function() {
	        Route::get('/',                                             'VehicleCategoriesController@index')->name('index');
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
	});
});