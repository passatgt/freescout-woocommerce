<?php

namespace Modules\WooCommerce\Providers;

use App\Customer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class WooCommerceServiceProvider extends ServiceProvider
{

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Run enrichment for
        \Eventy::addAction('customer.profile_data', function($customer, $conversation) {
					$email_address = $customer->getMainEmail();
					$public_key = config('woocommerce.public_key');
					$secret_key = config('woocommerce.secret_key');
					$domain = config('woocommerce.domain');
					$email_address = 'info@visztpeter.me';
					$email_address_id = preg_replace('/[^A-Za-z0-9\-]/', '', $email_address);
					$user_locale = session('user_locale');
					if($email_address) {
						?>
						<style type="text/css">
							.wc-order {
								background: #f1f3f5;
								border-radius: 5px;
								padding: 10px;
								margin: 0 0 10px 0;
							}

							.wc-order-info-row {
								display: flex;
								justify-content: space-between;
							}

							.wc-order-info-row span {
								text-transform: capitalize;
							}
						</style>
						<script>

						function setWithExpiry(key, value, ttl) {
							const now = new Date()
							const item = {
								value: value,
								expiry: now.getTime() + ttl,
							}
							localStorage.setItem(key, JSON.stringify(item))
						}

						function getWithExpiry(key) {
							const itemStr = localStorage.getItem(key)
							// if the item doesn't exist, return null
							if (!itemStr) {
								return null
							}
							const item = JSON.parse(itemStr)
							const now = new Date()
							// compare the expiry time of the item with the current time
							if (now.getTime() > item.expiry) {
								// If the item is expired, delete the item from storage
								// and return null
								localStorage.removeItem(key)
								return null
							}
							return item.value
						}

						//Setup php variables
						var domain = '<?php echo $domain; ?>';
						var locale = '<?php echo $user_locale; ?>';
						var email_address_id = '<?php echo $email_address_id; ?>';

						//Workaround, since jQuery is loaded at the end of the page, $ is not available yet
						setTimeout(function wait(){
							if(!window.$) return setTimeout(wait, 100);

							//Check if already stored
							var order_data = getWithExpiry('wc_customer_'+email_address_id);

							//If expired or doesn't exists, fetch new orders
							if(!order_data) {
								get_orders();
							} else {
								display_orders(order_data);
							}

							//Fetch orders using WC rest api
							function get_orders() {
								$.ajax({
									type: 'GET',
									url: domain+'wp-json/wc/v2/orders',
									data: {consumer_key: '<?php echo $public_key; ?>', consumer_secret: '<?php echo $secret_key; ?>', search: '<?php echo $email_address; ?>', _jsonp: 'callback'},
									dataType: 'jsonp',
									jsonpCallback: 'callback',
									success: function (jsonp) {

										//Cache for 1 hour
										setWithExpiry('wc_customer_'+email_address_id, jsonp, 60*60);

										//Display
										display_orders(jsonp);

									}
								});
							}

							//Simply append data to sidebar
							function display_orders(orders) {
								$('.wc-orders').remove();
								$('.customer-extra').append('<div class="wc-orders"><h4>Recent orders</h4></div>');

								orders.forEach(function(order){
									var total = new Intl.NumberFormat(locale, { style: 'currency', currency: order.currency }).format(order.total);
									var edit_link = domain+'wp-admin/post.php?post='+order.id+'&action=edit';
									var date = new Date(order.date_created);
									var datetime = Intl.DateTimeFormat(locale).format(date)
									var html = '<div class="wc-order">'+
														 '<div class="wc-order-info-row"><a target="_blank" href="'+edit_link+'">'+order.number+'</a> <span>'+total+'</span></div>'+
														 '<div class="wc-order-info-row"><span>'+datetime+'</span> <span>'+order.status+'</span></div>'+
														 '</div>';

									$('.wc-orders').append(html);
								});
							}

						}, 100);

						</script>

					<?php
					}

        }, 10, 2);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('woocommerce.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'woocommerce'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {

			$viewPath = resource_path('views/modules/woocommerce');

			$sourcePath = __DIR__.'/../Resources/views';

			$this->publishes([
					$sourcePath => $viewPath
			],'views');

			$this->loadViewsFrom(array_merge(array_map(function ($path) {
					return $path . '/modules/woocommerce';
			}, \Config::get('view.paths')), [$sourcePath]), 'woocommerce');

    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
