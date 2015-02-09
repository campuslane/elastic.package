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
	 * @param  string $indexName
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

		return $this->client->indices()->create( ['index'=>$indexName] );
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

		return  ['error' => "Index with the name: $indexName didn't exist"];
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
		if ($indexName)  return $this->singleIndexData($indexName);
		
		return $this->allIndexesData();
	}


	/**
	 * All indexes data
	 * @return array
	 */
	public function allIndexesData()
	{
		$indexes = [];

		$stats = $this->getStats();

		if ( isset($stats['indices']) and is_array($stats['indices']) )
		{
			foreach ($stats['indices'] as $index => $value)
			{
				$indexes[] = $this->singleIndexData($index);
			}
		}

		return $indexes;
	}


	public function singleIndexData($indexName)
	{
		$output = [];

		if ( ! $this->exists($indexName) ) return ['error' => "Index: $indexName doesn't exist."];

		$output = [
			'name' => $indexName, 
			'aliases' => $this->getIndexAliases($indexName), 
		]; 

		return  $output + $this->formatStats($indexName);

	}


	/**
	 * Format stats output.
	 * 
	 * @param  string $indexName
	 * @return array 
	 */
	protected function formatStats($indexName)
	{
		$output = [];

		$stats = $this->getStats($indexName);
		
		if ( isset($stats['indices'][$indexName]) )
		{
			if ( $bytes = $stats['indices'][$indexName]['primaries']['store']['size_in_bytes'] )
			{
				$output['size'] = $this->formatBytes($bytes);
				$output['docs'] = $stats['indices'][$indexName]['primaries']['docs']['count'];
			}
		}

		return $output;
	}


	/**
	 * Get index stats.
	 * 
	 * @param  string $indexName 
	 * @return array
	 */
	public function getStats($indexName = '')
	{
		$params = ['index'=>$indexName] ?: [];
		return $this->client->indices()->stats($params);
	}


	/**
	 * Get the Index Aliases.
	 * 
	 * @param  string $indexName
	 * @return string  comma separated list of alias names
	 */
	public function getIndexAliases($indexName)
	{
		$params['index'] = $indexName;
		$response = $this->client->indices()->getAliases($params);

		if( $aliases = isset($response[$indexName]['aliases']) ? $response[$indexName]['aliases'] : false ) 
		{
			$output = '';

			foreach($aliases as $alias => $value)
			{
				$output .= $alias . ', ';
			}

			return trim($output, ', ');
		}

		return '';
	}


	/**
	 * Format bytes into megabytes.
	 * 
	 * @param  integer  $size    (bytes)
	 * @param  integer $precision
	 * @return string      
	 */
	protected function formatBytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');   

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}

}