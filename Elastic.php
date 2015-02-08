<?php namespace CampusLane\ElasticSearch;

use CampusLane\ElasticSearch\Services\ClientTrait;
use CampusLane\ElasticSearch\Services\Indexing;
use CampusLane\ElasticSearch\Services\Mapping;
use CampusLane\ElasticSearch\Services\Utilities;
use Illuminate\Contracts\Foundation\Application;


/**
 * Elastic
 * 
 * A single entry point for managing Elasticsearch.
 * It includes direct access to the official Elasticsearch\Client class 
 * and to other classes that enhance the client's functionalty.
 *
 */

class Elastic  {

	/**
	 * Provide access to ElasticSearch\Client.
	 */
	use ClientTrait;


	/**
	 * The Laravel application instance.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;


	/**
	 * Injects the laravel application instance.
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;	
	}


	/**
	 * Get the Elasticsearch official PHP client.
	 * 
	 * @return Elasticsearch\Client instance
	 */
	public function client()
	{
		return $this->getElasticSearchClient();
	}


	/**
	 * Manage Elasticsearch indexes/Indexing.
	 * 
	 * @return CampusLane\ElasticSearch\Services\Indexing
	 */
	public function indexing()
	{
		return $this->app['ElasticIndexing'];
	}


	/**
	 * Manage Elasticsearch mapping.
	 * 
	 * @return CampusLane\ElasticSearch\Services\Mapping
	 */
	public function mapping()
	{
		return $this->app['ElasticMapping'];
	}


	/**
	 * Elasticsearch misc utilities.
	 *
	 * @return   CampusLane\ElasticSearch\Services\Utilities
	 */
	public function utilities()
	{
		return $this->app['ElasticUtilities'];
	}


}