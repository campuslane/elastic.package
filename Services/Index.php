<?php namespace CampusLane\ElasticSearch\Services;

use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;
use Elasticsearch\Client;

class Index  {
	
	/**
	 * The official elasticsearch php client
	 * @var Elasticsearch\Client
	 */
	protected $client;


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
	public function add($indexName) 
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

		$indexes = Collection::make($indexes);

		$indexes = $indexes->SortBy('settings.creation_date');

		return $indexes;
	}


	/**
	 * Sing Index Data
	 * @param  string $indexName
	 * @return array
	 */
	public function singleIndexData($indexName)
	{
		$output = [];

		if ( ! $this->exists($indexName) ) return ['error' => "Index: $indexName doesn't exist."];

		$output = [
			'name' => $indexName, 
			'aliases' => $this->getIndexAliases($indexName), 
		]; 

		return  $output + $this->formatStats($indexName) + $this->formatSettings($indexName);

	}


	/**
	 * Aliases data.
	 * 
	 * @return array
	 */
	public function aliasesData()
	{
		$output = [];

		$indexes = $this->client->indices()->getAliases();

		foreach ($indexes as $indexName =>$index) 
		{
			if ( isset($index['aliases']) and is_array($index['aliases']) )
			{
				foreach( $index['aliases'] as $alias => $values)
				{
					$output[] = ['name' => $alias, 'index' => $indexName];
				}
			}
		}

		return $output;
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
	 *  Get Index docs count
	 * 
	 * @param  string $index
	 * @return array   (with docs)
	 */
	public function getIndexDocsCount($index)
	{
		$output = [];

		$stats = $this->getStats($index);

		if ( isset($stats['indices'][$index]['primaries']['docs']['count']) )
		{
			return $output['docs'] = $stats['indices'][$index]['primaries']['docs']['count'];	
		}

		return $output['docs'] = "Couldn't retrieve Docs Count";

	}


	/**
	 *  Get index types
	 * 
	 * @param  string $index
	 * @return array   (with types)
	 */
	public function getIndexTypes($index)
	{
		$types = '';

		$indexData = $this->client->indices()->get(['index'=>$index]);

		foreach ($indexData[$index]['mappings'] as $type => $mapping)
		{
			$types .= $type . ', ';
		}

		return trim($types, ', ');

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
	 * Get settings.
	 * 
	 * @param  string $indexName
	 * @return array
	 */
	public function getSettings($indexName)
	{
		$settings =   $this->client->indices()->getSettings(['index'=>$indexName]);

		$settings[$indexName]['settings']['index']['creation_date'] = substr($settings[$indexName]['settings']['index']['creation_date'], 0, -3);

		return $settings;
		

	}


	/**
	 * Format settings.
	 * 
	 * @param  array $settings
	 * @return array
	 */
	public function formatSettings($indexName)
	{
		$output = [];
		$settings = $this->getSettings($indexName);
		$output['settings'] =  isset($settings[$indexName]['settings']['index']) ? $settings[$indexName]['settings']['index'] : [];
		return $output;
	}
	


	/**
	 * Get the Index Aliases.
	 * 
	 * @param  string $index
	 * @return string  comma separated list of alias names
	 */
	public function getIndexAliases($index)
	{

		$params['index'] = $index;
		$response = $this->client->indices()->getAliases($params);

		if( $aliases = isset($response[$index]['aliases']) ? $response[$index]['aliases'] : [] ) 
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
	 * Get index creation date
	 * @param  string $index
	 * @return string 
	 */
	public function getIndexCreationDate($index) 
	{

		$indexData = $this->client->indices()->get(['index'=> $index]);

		if (isset($indexData[$index]['settings']['index']['creation_date']))
		{
			$timestamp = $indexData[$index]['settings']['index']['creation_date'];

			$timestamp = $this->trimTimestamp($timestamp, 3);

			return \Carbon\Carbon::createFromTimeStamp($timestamp)->toDateTimeString();
			
		}

		return "Couldn't retrieve creation date";
	}


	/**
	 * Drop alias
	 * @param  string $alias
	 * @param  string $index
	 * @return array
	 */
	public function deleteAlias($alias, $index)
	{
		$params = ['name'=>$alias, 'index'=>$index];

		return $this->client->indices()->deleteAlias($params);
	}


	/**
	 * Active index
	 * Find the active index based on the active index alias.
	 * 
	 * @param  string $activeIndexAlias
	 * @return string  (active index name)
	 */
	public function activeIndex($activeIndexAlias)
	{
		$indexes = $this->client->indices()->getAliases();



		foreach( $indexes as $index => $aliases )
		{
			if ( $aliases ) 
			{
				foreach( $aliases['aliases'] as $alias => $value )
				{
					if ($alias == $activeIndexAlias)
					{	
						return $index;
					}
				}
			}
		}

		return '';
	}


	/**
	 * Activate index
	 * 
	 * @param  string $currentIndex   
	 * @param  string $newIndex        
	 * @param  string $activeIndexAlias 
	 * @return array              
	 */
	public function activateIndex($currentIndex, $newIndex, $activeIndexAlias)
	{
		// delete alias from current index if it's there
		$params = ['name'=>$activeIndexAlias, 'index'=>$currentIndex];

		if ( $this->client->indices()->existsAlias($params) )
		{
			$this->client->indices()->deleteAlias($params);
		}

		// add alias to new index if it's not there yet
		$params = ['name'=>$activeIndexAlias, 'index'=>$newIndex];

		if ( ! $this->client->indices()->existsAlias($params) )
		{
			$this->client->indices()->putAlias($params);
		}

		return ['done'];
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
		$suffixes = array('bytes', 'kb', 'Mb', 'G', 'T');   

		return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
	}


	/**
	 * Trim timestamp
	 * We need to trim 3 chars off  the elasticsearch timestamp.
	 *  
	 * @param  integer  $timestamp
	 * @param  integer $chars
	 * @return integer 
	 */
	protected function trimTimestamp($timestamp, $chars = 3)
	{
		return substr($timestamp, 0, -$chars);
	}
}