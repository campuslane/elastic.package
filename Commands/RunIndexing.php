<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;


/**
 * Run Indexing
 */

class RunIndexing implements SelfHandling {

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
		$indexing = new $indexingClass();
		return $indexing->handle();
	}

}
