<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;

/**
 * Get Active Index
 */

class GetActiveIndex implements SelfHandling {

	/**
	 * Active Index Alias
	 * @var string
	 */
	protected $activeIndexAlias;


	/**
	 * Constructor
	 */
	public function __construct($activeIndexAlias)
	{
		$this->activeIndexAlias = $activeIndexAlias;
	}

	/**
	 * Execute the command.
	 *
	 * @return array
	 */
	public function handle(Elastic $elastic)
	{
		return $elastic->activeIndex($this->activeIndexAlias);
	}

}
