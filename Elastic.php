<?php namespace CampusLane\ElasticSearch;

use CampusLane\ElasticSearch\Services\Index;
use Illuminate\Contracts\Foundation\Application;
use Elasticsearch\Client;


/**
 * Elastic
 * 
 * A single entry point for managing Elasticsearch.  Provides 
 * a wrapper for the official Elasticsearch PHP Client.
 *
 */

class Elastic  {

	/**
	 * The Laravel application instance.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;

	/**
	 * Index instance
	 * @var CampusLane\ElasticSearch\Services\Index
	 */
	protected $index;


	/**
	 * Injects the laravel application instance.
	 */
	public function __construct(Application $app, Index $index)
	{
		$this->app = $app;	
		$this->index = $index;
	}

	/**
	 * The official elasticsearch php client
	 * 
	 * @return instance of Elasticsearch\Client
	 */
	public function client()
	{
		return new Client();
	}


	/**
	 * Check if an index exists.
	 * 
	 * @param  string $name
	 * @return array
	 */
	public function indexExists($name)
	{
		return $this->index->exists($name);
	}


	/**
	 * Drop an index.
	 * 
	 * @param  string $name
	 * @return array
	 */
	public function indexDrop($name)
	{
		return $this->index->drop($name);
	}


	/**
	 * Get data about all indexes or specify a specific one.
	 * 
	 * @param  string $index
	 * @return array
	 */
	public function indexReportData($index = '')
	{
		return $this->index->reportData($index);
	}


}