<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use App;


/**
 * Run Mapping
 */

class RunMapping implements SelfHandling {


	/**
	 * Index name for mapping
	 * @var string
	 */
	protected $index;

	/**
	 * Type name for mapping
	 * @var [type]
	 */
	protected $type;


	/**
	 * Constructor
	 * @param string $index (the index to map)
	 */
	public function __construct($index, $type)
	{
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
		// get app mapping class from config
		$mappingClass = config('elastic.mapping.class');

		// instantiate the class and run the method
		$mapping = new $mappingClass(App::make('Elastic'), $this->index);
		
		return $mapping->handle($this->type);
	}

}
