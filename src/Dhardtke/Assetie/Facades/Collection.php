<?php namespace Dhardtke\Assetie\Facades;

use Illuminate\Support\Facades\Facade;

class Collection extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'collection'; }
}