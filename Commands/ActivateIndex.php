<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Collection;
use CampusLane\ElasticSearch\Elastic;

/**
 * Activate Index
 */

class ActivateIndex  implements SelfHandling {

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
	public function __construct($currentIndex, $newIndex, $activeIndexAlias)
	{
		$this->currentIndex = $currentIndex;
		$this->newIndex = $newIndex;
		$this->activeIndexAlias = $activeIndexAlias;
	}

	/**
	 * Execute the command.
	 *
	 * @return array
	 */
	public function handle(Elastic $elastic)
	{
		return $elastic->activateIndex($this->currentIndex, $this->newIndex, $this->activeIndexAlias);
	}

}
