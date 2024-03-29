<?php namespace CampusLane\ElasticSearch\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use App;

/**
 * Update Settings
 */

class UpdateSettings implements SelfHandling {

	/**
	 * Execute the command.
	 *
	 * @return array
	 */
	public function handle()
	{
		// get app settings class from config
		$settingsClass = config('elastic.settings.class');
		
		// instantiate the class and run the method
		$settings = new $settingsClass( App::make('Elastic') );
		return $settings->handle();
	}

}
