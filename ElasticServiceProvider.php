<?php namespace CampusLane\ElasticSearch;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\Client;
use CampusLane\ElasticSearch\Services\Indexing;
use CampusLane\ElasticSearch\Services\Mapping;
use CampusLane\ElasticSearch\Services\Utilities;
use CampusLane\ElasticSearch\Services\Reporting;

class ElasticServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// nothing to boot
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
		$this->registerElasticSearchClient();
		$this->registerElastic();
		$this->registerElasticIndexing();
		$this->registerElasticMapping();
		$this->registerElasticUtilities();
		$this->registerElasticReporting();
		
	}

	/**
	 * Register Elasticsearch client (official php client from Elasticsearch)
	 * 		
	 * @return void
	 */
	protected function registerElasticSearchClient()
	{
		$this->app->bindShared('ElasticSearchClient', function($app)
		{
			return new Client();
		});

	}

	/**
	 * Register Elastic Instance
	 *
	 * @return void
	 */
	protected function registerElastic()
	{
		$this->app->bindShared('Elastic', function($app)
		{
		    	return new Elastic($app);
		});
	}


	/**
	 * Register Elastic Indexing Instance
	 * 
	 * @return void
	 */
	protected function registerElasticIndexing()
	{
		$this->app->bindShared('ElasticIndexing', function($app)
		{
		    	return new Indexing();
		});
	}


	/**
	 * Register Elastic Mapping Instance
	 * 
	 * @return void
	 */
	protected function registerElasticMapping()
	{
		$this->app->bindShared('ElasticMapping', function($app)
		{
		    	return new Mapping();
		});
	}


	/**
	 * Register Elastic Utilities Instance
	 * 
	 * @return void
	 */
	protected function registerElasticUtilities()
	{
		$this->app->bindShared('ElasticUtilities', function($app)
		{
		    	return new Utilities();
		});
	}


	/**
	 * Register Elastic Reporting Instance
	 * 
	 * @return void
	 */
	protected function registerElasticReporting()
	{
		$this->app->bindShared('ElasticReporting', function($app)
		{
		    	return new Reporting($app['ElasticIndexing']);
		});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [

			'Elastic', 
			'ElasticSearchClient', 
			'ElasticIndexing', 
			'ElasticMapping', 
			'ElasticUtilities', 
			'ElasticReporting', 
		];

	}

}
