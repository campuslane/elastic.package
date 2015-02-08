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
	 * Get Indexes
	 * Elasticsearch gives us the indexes information in a single 
	 * string with line breaks.  We have to convert this into an array 
	 * of information. 
	 * 
	 * @param  string $alias
	 * @return array   Includes indexes, header, and aliasedIndex
	 */
	public function getIndexes($alias)
	{
		$output = [];
		
		// get indexes with headers
		$indexes = $this->client->cat()->indices(['v'=>true]);
		
		// convert elasticsearch string into an array of data
		$indexes = str_replace("\n", ' ', $indexes);
		$indexes = preg_split('/\s+/', $indexes);
		$indexes = array_filter($indexes, 'strlen');
		$indexes = array_chunk($indexes, 9);

		// array to map the headers 
		$headerMap = [

			0 => 'health', 
			1 => 'status', 
			2 => 'index', 
			3 => 'pri', 
			4 => 'rep', 
			5 => 'docs.count', 
			6 => 'docs.deleted', 
			7 => 'store.size', 
			8 => 'pri.store.size', 
		];

		// assign associate array keys using header map
		foreach($indexes as $key => $index) 
		{
			foreach($index as $k => $value)
			{
				unset($indexes[$key][$k]);
				$indexes[$key][$headerMap[$k]] = $value;
			}
		}

		// put the indexes in a collection and sort by name
		$indexes = new Collection($indexes);
		$indexes = $indexes->sortBy(function($index){return $index['index'];});

		// set the header row & remove it from indexes
		$header = isset($indexes[0]) ? $indexes[0] : '';
		if ($header) unset($indexes[0]);

		// get the alias
		$alias = $this->client->indices()->getAlias(['name'=>$alias]);

		// set the index that has the alias
		foreach($alias as $index => $alias)
		{
			$aliasedIndex = $index;
			break;
		}

		// set up and return output
		$output['header'] = $header;
		$output['aliasedIndex'] = $aliasedIndex;
		$output['indexes'] = $indexes;

		return $output;
	}


	/**
	 * Create a new elasticsearch index. 
	 */
	public function createIndex($index)
	{
		// append the date to the index name
		$base = $index . '-' . \Carbon\Carbon::now('America/Vancouver')->toDateString();

		// check up to 20 iterations
		for ($i = 1; $i <= 20; $i++)
		{
			$index =  $base . '-n' . $i;

			if( ! $this->indexExists($index) )
			{
				return $this->addIndex($index);
			} 
			
		}

		return false;
		
	}


	/**
	 * Check if index exists.
	 * 
	 * @param  string $index index name
	 * @return boolean
	 */
	private function indexExists($index)
	{
		return $this->client->indices()->exists(['index'=>$index]);
	}


	/**
	 * Add the new index.
	 * @param string $index index name
	 */
	private function addIndex($index) 
	{
		return $this->client->indices()->create(['index'=>$index]);
	}


	/**
	 * Delete an elastic search index.  
	 * 
	 * @param  string $index index name
	 * @return  integer
	 */
	public function dropIndex($index)
	{
		$data['index'] = $index;

		// try to delete the index
		try
		{
			$response = $this->client->indices()->delete($data);
		} 

		// if we got the missing 404 exception, there was no index to delete
		catch (Elasticsearch\Common\Exceptions\Missing404Exception $e) 
		{
			return 2;	
		}
		
		return  1;
	}

}