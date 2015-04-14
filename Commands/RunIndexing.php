<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use App;

/**
 * Run Indexing
 */

class RunIndexing implements SelfHandling {

	/**
	 * Index name for mapping
	 * @var string
	 */
	protected $index;

	/**
	 * From value
	 * @var integer
	 */
	protected $from;


	/**
	 * Take value
	 * @var integer
	 */
	protected $take;


	/**
	 * Type value
	 * @var string
	 */
	protected $type;


	/**
	 * Constructor
	 * @param string $index (the index to map)
	 */
	public function __construct($index, $type, $from, $take)
	{
		$this->from = $from;
		$this->take = $take;
		$this->index = $index;
		$this->type = $type;
	}


	/**
	 * Execute the command.
	 *
	 * @return array
	 */
	public function handle()
	{
		// get app indexing class from config
		$indexingClass = config('elastic.indexing.class');

		// instantiate the class and run handle method
		$indexing = new $indexingClass(App::make('Elastic'));
		return $indexing->handle($this->index, $this->type, $this->from, $this->take);
	}

}
