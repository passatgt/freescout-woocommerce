<?php

namespace Modules\WooCommerce\Providers;

use App\Customer;
use App\Option;
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
			\Eventy::addAction('conversation.after_prev_convs', function($customer, $conversation, $mailbox) {

				$template_vars = [
					'customer' => $customer,
					'conversation' => $conversation,
					'domain' => Option::get('wc_domain', \Config::get('app.wc_domain')),
					'locale' => session('user_locale')
				];
				echo view('woocommerce::orders', $template_vars);

			}, 10, 3);

			// Create settings menu
			\Eventy::addFilter('settings.sections', function($sections) {
				$sections['woocommerce'] = ['title' => __('WooCommerce'), 'icon' => 'shopping-cart', 'order' => 400];
				return $sections;
			}, 20, 1);

			// Setup settings fields
			\Eventy::addFilter('settings.sections', function($params, $section) {
				if($section == 'woocommerce') {
					$params = [
							'settings' => [
									'wc_domain' => [
											'env' => 'WC_DOMAIN',
									],
									'wc_public_key' => [
											'env' => 'WC_PUBLIC_KEY',
									],
									'wc_private_key' => [
											'env' => 'WC_PRIVATE_KEY',
									]
							],
					];
				}
				return $params;
			}, 20, 2);

			// Load saved options
			\Eventy::addFilter('settings.section_settings', function($settings, $section) {
				if($section == 'woocommerce') {
					$settings = [
						'wc_domain'   => Option::get('wc_domain', \Config::get('app.wc_domain')),
						'wc_public_key'   => Option::get('wc_public_key', \Config::get('app.wc_public_key')),
						'wc_private_key'   => Option::get('wc_private_key', \Config::get('app.wc_private_key')),
					];
				}
				return $settings;
			}, 20, 2);

			// Display the settings page template
			\Eventy::addFilter('settings.view', function($view, $section) {
				if($section == 'woocommerce') {
					$view = 'woocommerce::settings';
				}
				return $view;
			}, 20, 2);

			// Register JS files
			\Eventy::addFilter('javascripts', function($value) {
				array_push($value, '/modules/woocommerce/js/main.js');
				array_push($value, '/modules/woocommerce/js/laroute.js');
				return $value;
			}, 20, 1);

			// Add module's css file to the application layout
			\Eventy::addFilter('stylesheets', function($value) {
				array_push($value, '/modules/woocommerce/css/style.css');
				return $value;
			}, 20, 1);

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
