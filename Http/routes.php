<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\WooCommerce\Http\Controllers'], function()
{
    Route::get('/', 'WooCommerceController@index');
});
