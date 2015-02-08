<?php namespace CampusLane\ElasticSearch\Services;

use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;
use Elasticsearch\Client;

class Indexing  {

	use ClientTrait;
	

	/**
	 * The official elasticsearch php client
	 * @var Elasticsearch\Client
	 */
	public $client;


	/**
	 * Get the Elasticsearch client.
	 */
	public function __construct() 
	{
		$this->client = $this->getElasticSearchClient();
	}


	/**
	 * Check if index exists.
	 * 
	 * @param  string $index index name
	 * @return boolean
	 */
	public function indexExists($index)
	{
		return $this->client->indices()->exists(['index'=>$index]);
	}


	/**
	 * Add a new index.
	 * If successful the array will have ['acknowledged' => 1]
	 * 
	 * @param string $index index name
	 * @return  array
	 */
	public function createIndex($index) 
	{
		if ( $this->indexExists($index) ) 
		{
			return ['error' => "Index with the name: $index already exists"];
		}

		return $this->client->indices()->create(['index'=>$index]);
	}


	/**
	 * Delete an elastic search index.  
	 *  If successful the array will have ['acknowledged' => 1]
	 *  
	 * @param  string $index index name
	 * @return  array
	 */
	public function dropIndex($index)
	{
		$data['index'] = $index;

		if ( $this->indexExists($index) )
		{
			return $this->client->indices()->delete($data);
		}

		return  ['error' => "Index with the name: $index didn't exist"];
	}

}