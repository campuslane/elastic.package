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
	public $index;


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
	public function indexesReportData($indexName = '')
	{
		return $this->index->reportData($indexName);
	}


	/**
	 * Get aliases data
	 * @return array
	 */
	public function aliasesData()
	{
		return $this->index->aliasesData();
	}


	/**
	 * Drop alias.
	 * 
	 * @param  string $alias
	 * @param  string $index
	 * @return array
	 */
	public function deleteAlias($alias, $index)
	{
		return $this->index->deleteAlias($alias, $index);
	}


	/**
	 * Active Index
	 * 
	 * @param  string $activeIndexAlias
	 * @return string
	 */
	public function activeIndex($activeIndexAlias)
	{
		return $this->index->activeIndex($activeIndexAlias);
	}


	/**
	 * Activate Index
	 * 
	 * @param  string $currentIndex
	 * @param  string $newIndex
	 * @param  string $activeIndexAlias
	 * @return array          
	 */
	public function activateIndex($currentIndex, $newIndex, $activeIndexAlias)
	{
		return $this->index->activateIndex($currentIndex, $newIndex, $activeIndexAlias);
	}

	/**
	 *  Get Index docs count
	 * 
	 * @param  string $index
	 * @return array   (with docs)
	 */
	public function getIndexDocsCount($index)
	{
		return $this->index->getIndexDocsCount($index);
	}


	/**
	 *  Get index types
	 * 
	 * @param  string $index
	 * @return array   (with types)
	 */
	public function getIndexTypes($index)
	{
		return $this->index->getIndexTypes($index);

	}


	/**
	 * Get the Index Aliases.
	 * 
	 * @param  string $index
	 * @return string  comma separated list of alias names
	 */
	public function getIndexAliases($index)
	{
		return $this->index->getIndexAliases($index);
	}


	/**
	 * Get index creation date.
	 * 
	 * @param  string $index 
	 * @return string  
	 */
	public function getIndexCreationDate($index) 
	{
		return $this->index->getIndexCreationDate($index);
	}


}