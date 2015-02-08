<?php namespace CampusLane\ElasticSearch\Services;


class Utilities {

	use ClientTrait;
	
	/**
	 * Get Randomizer Seed
	 * We need some kind of numeric to use as a seed for the elasticsearch
	 * random_score function.  We'll use the session token reduced to only 
	 * numerics.  If none, then we just use a random string
	 * 	
	 * @return integer 
	 */
	public function getRandomizerSeed()
	{
		// get the session token or default to random number
		$session_id = \Session::get('_token') ?: 123;
		
		// strip out non-numeric characters to create randomizer seed
		return preg_replace('/[^0-9]/','',$session_id);
	}

}