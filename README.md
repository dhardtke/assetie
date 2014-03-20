assetie
=======

Assetie is my laravel package for managing assets in Laravel 4.

__This package is still wip, that means it is in an early stage and needs much work that has to be done until Assetie can be used in a production environment!__


Installation
============
1. Require Assetie by adding it to the "require" key of the composer.json file:

		"dhardtke/assetie": "dev-master"

	Run "composer update" to update all your dependencies and install Assetie.

2. Include Assetie in your Laravel project by adding it to the app's config:

	In **app/config/app.php**:
	
	Add `'Dhardtke\Assetie\AssetieServiceProvider'` to the `'providers'` array
	and `'Collection'	  => 'Dhardtke\Assetie\Facades\Collection'` to the `aliases` array
	
That's it! Now you can start managing your assets.
