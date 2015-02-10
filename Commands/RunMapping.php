<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;


/**
 * Run Mapping
 */

class RunMapping implements SelfHandling {

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
		$mapping = new $mappingClass();
		return $mapping->handle();
	}

}
