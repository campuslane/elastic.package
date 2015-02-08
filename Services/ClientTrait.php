<?php namespace CampusLane\ElasticSearch\Services;


trait ClientTrait {

	public function getElasticSearchClient()
	{
		return new \Elasticsearch\Client();
	}
}