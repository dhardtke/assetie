<?php namespace Dhardtke\Assetie;

use Illuminate\Support\ServiceProvider;

class AssetieServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('dhardtke/assetie');
		// Load the asset collections if they're in app/collections.php
		if (file_exists($file = $this->app['path'].'/collections.php'))
            require $file;
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{	
		$this->app["collection"] = $this->app->share(function($app) {
			return new Collection;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('assetie');
	}

}