<?php namespace CampusLane\ElasticSearch\Services;


class Reporting {

	use ClientTrait;

	/**
	 * Instance of Elasticsearch client.
	 * @var Elasticsearch\Client
	 */
	protected $client;

	/**
	 * Instance of indexing
	 * @var CampusLane\ElasticSearch\Services\Indexing
	 */
	protected $indexing;

	public function __construct(Indexing $indexing)
	{
		$this->client = $this->getElasticSearchClient();
		$this->indexing = $indexing;
	}


	/**
	 * Get indexes Information
	 * Elasticsearch gives us the indices information in a single 
	 * string with line breaks.  We have to convert this into an array 
	 * of information. 
	 * 
	 * @param  string $alias
	 * @return array   Includes indices, header, and aliasedIndex
	 */
	public function getIndexesInfo($alias)
	{
		$output = [];
		
		// get indices with headers
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
	public function createDatedIndex($index)
	{
		// append the date to the index name
		$base = $index . '-' . \Carbon\Carbon::now(Config::get('app.timezone'))->toDateString();

		// check up to 20 iterations
		for ($i = 1; $i <= 20; $i++)
		{
			$index =  $base . '-n' . $i;

			if( ! $this->indexing->indexExists($index) )
			{
				return $this->indexing->addIndex($index);
			} 
			
		}

		return false;
		
	}


}