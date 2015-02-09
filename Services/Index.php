<?php namespace CampusLane\ElasticSearch\Services;

use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;
use Elasticsearch\Client;

class Index  {
	
	/**
	 * The official elasticsearch php client
	 * @var Elasticsearch\Client
	 */
	public $client;


	/**
	 * Inject the elasticsearch client.
	 */
	public function __construct(Client $client) 
	{
		$this->client = $client;
	}


	/**
	 * Check if index exists.
	 * 
	 * @param  string $index index name
	 * @return boolean
	 */
	public function exists($indexName)
	{
		return $this->client->indices()->exists(['index'=>$indexName]);
	}


	/**
	 * Add a new index.
	 * 
	 * If successful the array will have ['acknowledged' => 1]
	 * 
	 * @param string $indexName
	 * @return  array
	 */
	public function createIndex($indexName) 
	{
		if ( $this->exists($indexName) ) 
		{
			return ['error' => "Index with the name: $indexName already exists"];
		}

		return $this->client->indices()->create(['index'=>$indexName]);
	}


	/**
	 * Drop an elastic search index.  
	 * 
	 *  If successful the array will have ['acknowledged' => 1]
	 *  
	 * @param  string $indexName
	 * @return  array
	 */
	public function drop($indexName)
	{
		$params['index'] = $indexName;

		if ( $this->exists($indexName) )
		{
			return $this->client->indices()->delete($params);
		}

		return  ['error' => "Index with the name: $index didn't exist"];
	}


	/**
	 * Get index report data.
	 * 
	 * If no index name is provided, it returns a 
	 * report on all elasticsearch indexes.
	 * 
	 * @param  string $indexName (optional)
	 * @return array 
	 */
	public function reportData($indexName = '')
	{
		return ['the index report data'];
	}

}