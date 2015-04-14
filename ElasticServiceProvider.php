<?php namespace CampusLane\ElasticSearch;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\Client;
use CampusLane\ElasticSearch\Services\Index;


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
		
		$this->setViewsDirectory();
		$this->setConfigAndAssetPublishing();
	
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
		$this->registerElasticIndex();

		
	}


	/**
	 * Set the view directory for package
	 */
	public function setViewsDirectory()
	{

		$this->loadViewsFrom(__DIR__.'/Views', 'elastic');
	}


	/**
	 * Set config and asset publishing
	 */
	public function setConfigAndAssetPublishing()
	{
		$this->publishes([
    			__DIR__.'/Config/elastic.php' => config_path('elastic.php'),
    			__DIR__.'/Assets/js/elastic.js' => public_path('js/elastic/elastic.js'),
    			__DIR__.'/Assets/css/elastic.css' => public_path('css/elastic/elastic.css'),
		]);
	}

	/**
	 * Register the official Elasticsearch PHP Client instance.
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
	 * Register our wrapper Elastic instance
	 *
	 * @return void
	 */
	protected function registerElastic()
	{
		$this->app->bindShared('Elastic', function($app)
		{
		    	return new Elastic($app, $app['ElasticIndex']);
		});
	}


	/**
	 * Register index instance
	 * 
	 * @return void
	 */
	protected function registerElasticIndex()
	{
		$this->app->bindShared('ElasticIndex', function($app)
		{
		    	return new Index($app['ElasticSearchClient']);
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
			'ElasticIndex', 
		
		];

	}

}
