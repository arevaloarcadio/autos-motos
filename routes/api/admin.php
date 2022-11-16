<?php
use Illuminate\Support\Facades\Route;

// NOTA
// Las rutas definidas en este archivo poseen el prefijo "api/admin" y pasan por los middleware "jwt.verify" y "is.admin"
// Este archivo se carga desde el archivo RouteServiceProvider

Route::prefix('ads')->name('ads/')->group(function () {
    Route::get('/', 'AdsController@index')->name('index');

    Route::post('/filter', 'AdsController@index')->name('filter');

    Route::get('/byDealer/{dealer_id}', 'AdsController@byDealer')->name('byDealer');
    Route::get('/byDealerCount/{dealer_id}', 'AdsController@byDealerCount')->name('byDealerCount');
    Route::get('/bySource', 'AdsController@bySource')->name('bySource');
    Route::get('/countToday', 'AdsController@countAdsToday')->name('countAdsToday');
    Route::get('/countImportToday', 'AdsController@countAdsImportToday')->name('countAdsImportToday');
    Route::get('/byCsv/{csv_ad_id}', 'AdsController@byCsv')->name('byCsv');
    Route::get('/groupByCsv', 'AdsController@groupByCsv')->name('groupByCsv');
    Route::get('/{ad_id}', 'AdsController@show')->name('show');
    Route::post('/', 'AdsController@store')->name('store');
    Route::post('/search_advanced', 'AdsController@searchAdvanced')->name('searchAdvanced');
    Route::post('/count_search_advanced', 'AdsController@countSearchAdvanced')->name('searchAdvanced');

    Route::post('/search_advanced_mechanic', 'AdsController@searchAdvancedMechanic')->name('searchAdvancedMechanic');

    Route::post('/search_ads_like', 'AdsController@searchAdsLike')->name('search_ads_like');

    Route::post('/search_ads_like_title', 'AdsController@searchAdsLikeTitle')->name('search_ads_like');

    Route::post('/{ad_id}/rejected_comment', 'AdsController@storeCommentRejected')->name('storeCommentRejected');
    Route::post('/rejected_comment_individual_ads', 'AdsController@storeCommentsRejectedIndividual')->name('storeCommentRejected');
    Route::post('/{csv_ad_id}/ads_rejected_comment', 'AdsController@storeCommentsRejected')->name('ads_rejected_comment');
    Route::post('/{status}/approved_rejected', 'AdsController@setApprovedRejected')->name('store');
    Route::post('/{status}/approved_rejected_individual', 'AdsController@setApprovedRejectedIndividual')->name('store');
    Route::post('/{status}/approved_rejected_ads', 'AdsController@setApprovedRejected')->name('store');

    Route::post('/bulk-destroy', 'AdsController@bulkDestroy')->name('bulk-destroy');
    Route::post('/{ad}', 'AdsController@update')->name('update');
    Route::delete('/{ad}', 'AdsController@destroy')->name('destroy');

});

Route::prefix('truck-ads')->name('truck-ads/')->group(function () {
    Route::get('/', 'TruckAdsController@index')->name('index');
    Route::get('/promoted', 'TruckAdsController@truckAdsPromotedFrontPage')->name('truckAdsPromotedFrontPage');
    Route::post('/search/like', 'TruckAdsController@searchLike');
    Route::post('/bulk-destroy', 'TruckAdsController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{truckAd}', 'TruckAdsController@destroy')->name('destroy');
});

Route::prefix('users')->name('users/')->group(function () {
    Route::get('/', 'UsersController@index')->name('index');
    Route::get('/{user}', 'UsersController@show')->name('show');
    Route::get('/{user}/info_ads', 'UsersController@countAdsByUser')->name('show');
    Route::post('/', 'UsersController@store')->name('store');
    Route::post('/professional', 'UsersController@store_professional')->name('store_professional');
    Route::post('/validator_company_name', 'UsersController@validator_company_name')->name('store_professional');
    Route::post('/validator_dealer_show_room_name', 'UsersController@validator_dealer_show_room_name')->name('store_professional');
    Route::post('/validator_email', 'UsersController@validator_email')->name('store_professional');
    Route::post('/{user}/status', 'UsersController@setStatus')->name('setStatus');
    Route::post('/{user}', 'UsersController@update')->name('update');
    Route::post('/bulk-destroy', 'UsersController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{user}', 'UsersController@destroy')->name('destroy');
});

Route::prefix('auto-ads')->name('auto-ads/')->group(function () {
    Route::get('/', 'AutoAdsController@index')->name('index');
    Route::post('/', 'AutoAdsController@store')->name('store');
    Route::post('/{id}', 'AutoAdsController@update')->name('update');
    Route::get('/promoted', 'AutoAdsController@autoAdsPromotedFrontPage')->name('autoAdsPromotedFrontPage');
    Route::post('/search/like', 'AutoAdsController@searchLike');
    Route::post('/bulk-destroy', 'AutoAdsController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{autoAd}', 'AutoAdsController@destroy')->name('destroy');
});

Route::prefix('dealers')->name('dealers/')->group(function () {
    Route::get('/', 'DealersController@index')->name('index');
    Route::get('/{dealer}', 'DealersController@show')->name('show');
    Route::post('/', 'DealersController@store')->name('store');
    Route::post('/bulk-destroy', 'DealersController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{dealer}', 'DealersController@destroy')->name('destroy');
});

Route::prefix('dealer-show-rooms')->name('dealer-show-rooms/')->group(function () {
    Route::get('/', 'DealerShowRoomsController@index')->name('index');
    Route::post('/', 'DealerShowRoomsController@store')->name('store');
    Route::post('/bulk-destroy', 'DealerShowRoomsController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{dealerShowRoom}', 'DealerShowRoomsController@destroy')->name('destroy');
});

Route::prefix('makes')->name('makes/')->group(function () {
    Route::get('/', 'MakesController@index')->name('index');
    Route::get('/{id}/sub_models', 'MakesController@getSubmodels')->name('getSubmodels');
    Route::post('/', 'MakesController@store')->name('store');
    Route::post('/bulk-destroy', 'MakesController@bulkDestroy')->name('bulk-destroy');
    Route::post('/{make}', 'MakesController@update')->name('update');
    Route::delete('/{make}', 'MakesController@destroy')->name('destroy');
});

Route::prefix('markets')->name('markets/')->group(function () {
    Route::get('/', 'MarketsController@index')->name('index');
    Route::post('/', 'MarketsController@store')->name('store');
    Route::post('/bulk-destroy', 'MarketsController@bulkDestroy')->name('bulk-destroy');
    Route::post('/{market}', 'MarketsController@update')->name('update');
    Route::delete('/{market}', 'MarketsController@destroy')->name('destroy');
});

Route::prefix('models')->name('models/')->group(function () {
    Route::get('/', 'ModelsController@index')->name('index');
    Route::post('/', 'ModelsController@store')->name('store');
    Route::post('/bulk-destroy', 'ModelsController@bulkDestroy')->name('bulk-destroy');
    Route::post('/{model}', 'ModelsController@update')->name('update');
    Route::delete('/{model}', 'ModelsController@destroy')->name('destroy');
});

Route::prefix('shop-ads')->name('shop-ads/')->group(function () {
    Route::get('/', 'ShopAdsController@index')->name('index');
    Route::get('/promoted', 'ShopAdsController@shopAdsPromotedFrontPage')->name('shopAdsPromotedFrontPage');
    Route::post('/search_advanced', 'ShopAdsController@search_advanced')->name('store');
    Route::post('/search/like', 'ShopAdsController@searchLike');
    Route::post('/bulk-destroy', 'ShopAdsController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{shopAd}', 'ShopAdsController@destroy')->name('destroy');
});

Route::prefix('truck-ads')->name('truck-ads/')->group(function () {
    Route::get('/', 'TruckAdsController@index')->name('index');
    Route::get('/promoted', 'TruckAdsController@truckAdsPromotedFrontPage')->name('truckAdsPromotedFrontPage');
    Route::post('/search/like', 'TruckAdsController@searchLike');
    Route::post('/bulk-destroy', 'TruckAdsController@bulkDestroy')->name('bulk-destroy');
    Route::delete('/{truckAd}', 'TruckAdsController@destroy')->name('destroy');
});
