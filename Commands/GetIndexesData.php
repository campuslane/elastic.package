<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;

/**
 * Get Elasticsearch indexes.
 */

class GetIndexesData implements SelfHandling {


	/**
	 * Execute the command.
	 *
	 * @return array  Includes Indexes, Header, and Aliased Index
	 */
	public function handle(Elastic $elastic)
	{
		return $elastic->indexesReportData();
	}

}
