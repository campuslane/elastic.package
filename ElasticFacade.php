<?php namespace CampusLane\ElasticSearch;

use Illuminate\Support\Facades\Facade;

class ElasticFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Elastic'; }

}