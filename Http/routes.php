<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\WooCommerce\Http\Controllers'], function()
{
		Route::post('/woocommerce/ajax', ['uses' => 'WooCommerceController@ajax', 'laroute' => true])->name('woocommerce.ajax');
});
