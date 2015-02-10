<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;

/**
 * Get Elasticsearch indexes.
 */

class DeleteAlias implements SelfHandling {

	/**
	 * Alias
	 * @var string
	 */
	protected $alias;

	/**
	 * Index
	 * @var string
	 */
	protected $index;

	/**
	 * Constructor
	 */
	public function __construct($alias, $index)
	{
		$this->alias = $alias;
		$this->index = $index;
	}

	/**
	 * Execute the command.
	 *
	 * @return array
	 */
	public function handle(Elastic $elastic)
	{
		return $elastic->deleteAlias($this->alias, $this->index);
	}

}
