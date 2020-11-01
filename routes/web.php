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


/**
 * Auth pages
 * Auth::routes();
 */
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);


/**
 * Super Admin Routes
 */
Route::group(['middleware' => 'can:accessSuperAdmin, App\User', 'prefix'=>'settings', 'as'=>'settings.'], function(){
    Route::get('/companies', 'BuilderController@index')->name('companies');
    Route::get('/companies/fetch', 'CompaniesController@fetchCompanies')->name('fetch.companies');
}); 

/**
 * Builder Pages
 */
Route::get('/dashboard', 'BuilderController@index')->name('builder.dashboard');
Route::get('/restricted', 'BuilderController@index')->name('builder.dashboard');
Route::get('/builder', 'BuilderController@index')->name('builder');
Route::get('/builder/products', 'BuilderController@index')->name('builder.products');
Route::get('/builder/products/all', 'ProductsController@productsAPI')->name('builder.all.products'); 

Route::group(["middleware" => 'role:super-admin|administrator|free-user|basic-user|advanced-user|premium-user'], function() {
    Route::get('/settings/teams', 'BuilderController@index')->name('teams');
    Route::post('/settings/team/delete/{id}', 'SettingsController@deleteOrgUser')->name('delete.team');
    Route::post('/settings/team/update/{id}', 'SettingsController@updateOrgUser')->name('update.team');
    Route::post('/settings/team/save', 'SettingsController@saveOrgUser')->name('save.team');
    Route::post('/settings/team/search_data/{any}', 'SettingsController@searchData')->name('search.team'); 
});

Route::group(["middleware" => 'role:super-admin|administrator|editor|free-user|basic-user|advanced-user|premium-user'], function() {
    // Settings
        Route::get('/settings/watermarks', 'BuilderController@index')->name('settings.watermarks');
        Route::get('/settings/watermarks/all', 'WatermarksController@fetchWatermarksForUploadZone')->name('settings.watermarks.uploadzone'); // needs to transfer to vuex!!
        Route::get('/settings/watermarks/page/{page}', 'BuilderController@index')->name('settings.watermarks.page');
        Route::get('/settings/watermarks/fetch', 'WatermarksController@fetchWatermarks')->name('settings.watermarks.fetch');
        Route::post('/settings/watermark/add', 'WatermarksController@addWatermark')->name('settings.watermark.add');
        Route::get('/settings/watermark/edit/{id}', 'BuilderController@index')->name('settings.watermark.edit');
        Route::get('/settings/watermark/get/{id}', 'WatermarksController@getWatermark')->name('settings.get.watermark');
        Route::post('/settings/watermark/delete/{id}', 'WatermarksController@destroy')->name('settings.watermark.delete');
        Route::post('/settings/watermark/update/{id}', 'WatermarksController@update')->name('settings.watermark.update');
 
        // Organization
        Route::get('/settings/organization', 'BuilderController@index')->name('organization');
        Route::get('/settings/organization/fetch', 'SettingsController@fetchOrg')->name('fetch');
        Route::post('/settings/organization/update', 'SettingsController@updateOrg')->name('update');
        Route::get('/settings/get-org-users/{id}', 'SettingsController@getOrgUsers')->name('users'); 

        // Builder
        Route::get('/builder/product/new', 'BuilderController@index')->name('builder.new.product');
        Route::get('/builder/product/edit/{id}', 'BuilderController@edit')->name('builder.edit.product');

        Route::get('/builder/products/fetch/{id}', 'ProductsController@fetch')->name('builder.fetch.product');
        Route::post('/builder/products/searchProduct/{any}', 'ProductsController@searchProduct')->name('builder.search.product');
        Route::post('/builder/product/store', 'ProductsController@store')->name('builder.store.product');
        
        Route::get('/builder/product/upload-video', 'BuilderController@index')->name('builder.upload.video');
        Route::post('/video/store', 'VideoController@store')->name('upload.video'); 
        Route::get('/builder/scenes/product/{id}', 'ScenesController@scenesByProductId')->name('builder.scenes.by.product.id');
        Route::post('/builder/scene/store', 'ScenesController@store')->name('builder.store.scene');  

        // Media_files
        Route::post('/files/upload', 'FilesController@upload')->name('upload');
        Route::post('/files/apply_watermark/', 'FilesController@apply_watermark')->name('apply_watermark');
        Route::post('/files/remove_watermark/', 'FilesController@remove_watermark')->name('remove_watermark');

        Route::get('/files/fetch', 'FilesController@getItemImages')->name('fetch.item.files'); // has static, used in UploadVideo component
        Route::get('/display/file/{path}', 'FilesController@showImage')->name('show.file'); // has static, used in UploadVideo component

        Route::get('/user/files/{id}', 'FilesController@getUserFilesByID')->name('get.user.files');
        Route::post('/item/search/{any}', 'FilesController@searchData')->name('get.search.files');

        // Items Controller
        Route::get('/items/by-product/{id}', 'ItemsController@getItemsByProduct')->name('items.by.product');
        Route::post('/item/delete/{id}', 'ItemsController@destroy')->name('items.delete');
        // Save or replace depending on the request data
        Route::post('/item/save', 'ItemsController@saveItem')->name('items.save');

        // Panorama
        Route::get('/item/scenes/by-product/{id}', 'ItemsController@getScenesByProductId')->name('item.scenes');

        // Hotspots
        Route::post('/hotspot/new', 'HotspotsController@store')->name('hotspot.save');
        Route::post('/hotspot/update/{id}', 'HotspotsController@update')->name('hotspot.update');
        Route::post('/hotspot/set', 'HotspotsController@setHotspot')->name('hotspot.set');
        Route::post('/hotspot/delete/{id}', 'HotspotsController@destroy')->name('hotspot.destroy');
        Route::get('/hotspot/by-product/{id}/{panel}', 'HotspotsController@allHostspotsByProductId')->name('hotspot.by.product.id');
        Route::post('/hotspot/apply', 'HotspotsController@applyHotspot')->name('hotspot.apply');
        Route::get('/hotspot/settings/{id}', 'HotspotsController@fetchSettings')->name('hotspot.settings');
        Route::post('/hotspot/setting/delete', 'HotspotsController@deleteSetting')->name('hotspot.setting.delete');
        Route::get('/hotspot/product/{id}', 'HotspotsController@getProductHotspots')->name('hotspot.product');

        Route::get('/hotspot/all/interior/{id}', 'HotspotsController@getAllInteriorHotspotsByProductId')->name('hotspot.all.interior');

        // Videos
        Route::post('/video/save', 'VideosController@store')->name('video.save');
        Route::post('/video/update/{id}', 'VideosController@update')->name('video.update');
        Route::post('/video/delete/{id}', 'VideosController@destroy')->name('video.delete');
        Route::get('/video/all/{id}', 'VideosController@fetchAllVideos')->name('video.all');

        // Scenes
        Route::post('/scene/setposition', 'ItemsController@setPosition')->name('scene.position');

        /**
         * Accounts
         */
        Route::get('/settings/account', 'BuilderController@index')->name('settings.account');
        Route::get('/settings/account/fetch', 'SettingsController@fetchAccount')->name('settings.fetch.account');
        Route::post('/settings/account/update', 'SettingsController@updateAccount')->name('settings.update.account');
        Route::post('/settings/account/update_password', 'SettingsController@updateAccount_password')->name('settings.update.password');


        Route::get('/product/{slug}', 'ProductsController@show')->name('single.product');
        Route::post('/product/delete/{id}', 'ProductsController@destroy')->name('product.delete');
}); 