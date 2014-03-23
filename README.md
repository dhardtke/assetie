#Assetie
## What is Asetie?
Assetie is my laravel package for managing assets in Laravel 4.
It uses [Kris Wallsmith's](https://github.com/kriswallsmith) great Assetic for it's minifying and compressing of asset files.

__This package is still wip, that means it is in an early stage and needs much work that has to be done until Assetie can be used in a production environment!__

### Why another Asset Management Package for Laravel 4?
I tried several products over time and wasn't happy with them at all.

All I've wanted was a package which would be easy to use, clean and fast at the same time.

## Installation
1. Require Assetie by adding it to the "require" key of the composer.json file:

		"dhardtke/assetie": "dev-master"

    Run "composer update" to update all your dependencies and install Assetie.

2. Include Assetie in your Laravel project by adding it to the app's config:

    In **app/config/app.php**:

    - `'Dhardtke\Assetie\AssetieServiceProvider'` to the `'providers'` array
    - `'Collection'         => 'Dhardtke\Assetie\Facades\Collection'` to the `aliases` array

That's it! Now you can start managing your assets.

## Configuration

Run `php artisan config:publish dhardtke/assetie` from the command line in your Laravel installation directory to publish Assetie's config file.

Then you can edit **app/config/packages/dhardtke/assetie/config.php** as follows:
_______________
### Filters
    'filters' => array(
    	'.min.js' => array(
    	),
    	'.min.css' => array(
    		new \Assetic\Filter\CssRewriteFilter,
    		$uglifyCss,
    		new \Assetic\Filter\PhpCssEmbedFilter
    	),
    	'.js' => array(
    		$JSqueeze
    	),
    	'.less'	=> array(
    		$lessFilter,
    		$uglifyCss
    	),
    	'.css' => array(
    		new \Assetic\Filter\CssRewriteFilter,
    		$uglifyCss,
    		new \Assetic\Filter\PhpCssEmbedFilter
    	),
    )
Add specific Assetic filters to certain extensions. See the appropriate [Assetic filters overview](https://github.com/kriswallsmith/assetic#filters) for more details.

### Directories
    'directories'    => array(
    	'javascripts'	=> 'assets/javascripts',
    	'stylesheets'	=> 'assets/stylesheets'
    ),
    
These are the directories where Assetie will look for it's JavaScript and Stylesheet-Files.
If you want to change them, you can do it here.
_These paths are relative to the app path._

### Build Path
    'build_path'    => 'builds',
If you want, you can change the directory where Assetie will store it's builds.
By default it is set to app_public() . /builds, but if you want you can change it here.

_This path is always relative to the public path of Laravel_

## Usage
Create a file called **collections.php** next to your **routes.php**. That file will be automatically included by Assetie and holds your Asset Collections.

### Define your first collection
To create your own, first collection you have to use the following code inside of your collections.php:

    Collection::addCollection("base", function($collection) {
        $collection->add([
    		"style.css",
            "base.js"
    	]);
    });

You can now start using this collection by calling this function in your template (typically in your <head>):

    {{ Collection::stylesheets("base"}}
    {{ Collection::javascripts("base"}}

### Defining a collection that uses the base collection
If you now want to have a new collection that inherits the base collection, you can do it like this:

    Collection::addCollection("portal", function($collection) {
        $collection->includeCollection("base")
    	 ->add("portal.css");
    });
