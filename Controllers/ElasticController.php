<?php namespace CampusLane\ElasticSearch\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Bus;

use CampusLane\ElasticSearch\Elastic;
use CampusLane\ElasticSearch\Commands\GetIndexesData;
use CampusLane\ElasticSearch\Commands\GetAliasesData;
use CampusLane\ElasticSearch\Commands\GetActiveIndex;
use CampusLane\ElasticSearch\Commands\DeleteAlias;
use CampusLane\ElasticSearch\Commands\ActivateIndex;
use CampusLane\ElasticSearch\Commands\RunMapping;
use CampusLane\ElasticSearch\Commands\RunIndexing;

use Request;



class ElasticController extends BaseController {

	use DispatchesCommands, ValidatesRequests;
	/*
	|--------------------------------------------------------------------------
	| Elastic Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles all things elasticsearch related.
	| 
	|
	*/


	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Elastic $elastic)
	{
		$this->middleware('guest');
		$this->elastic = $elastic;
		$this->client = $elastic->client();
	}


	/**
	 * Show the elasticsearch indexes.
	 *
	 * @return Response
	 */
	public function getIndex()
	{	
		
		$indexes =  Bus::dispatch( new GetIndexesData() );
		$activeIndex = Bus::dispatch( new GetActiveIndex( config('elastic.index.alias') ) );
		
		return view('elastic::home', compact('indexes', 'activeIndex'));
	}


	/**
	 * Individual index info
	 * @param  string $index
	 * @return Response
	 */
	public function getIndexInfo($index)
	{

		$data = $this->indexData($index);
		
		return view('elastic::index', $data);
		
	}


	/**
	 * Get the index data
	 * @param  string $index 
	 * @return array 
	 */
	protected function indexData($index)
	{
		$cluster = $this->client->cluster()->state(['index'=>$index]);

		$state = isset($cluster['metadata']['indices'][$index]['state']) ? $cluster['metadata']['indices'][$index]['state'] : '';
		
		$indexInfo = $this->client->indices()->get(['index'=>$index]);

		$data = [

			'name'=> $index, 
			//'types' => $this->elastic->getIndexTypes($index), 
			'types' => config('elastic.types'), 
			'aliases' => $this->elastic->getIndexAliases($index), 
			'creation_date' => $this->elastic->getIndexCreationDate($index), 
			'json' => json_encode($indexInfo, JSON_PRETTY_PRINT), 
			'docs' => $this->elastic->getIndexDocsCount($index), 
			'state' => $state, 

		];

		$data['active'] =  ( trim($data['aliases']) == config('elastic.index.alias') ) ? true : false;

		return $data;
	}


	/**
	 * Delete alias
	 * @return array
	 */
	public function postDeleteAlias()
	{
		$alias =  Request::get('alias');
		$index = Request::get('index');

		return Bus::dispatch( new DeleteAlias($alias, $index) );
	}


	/**
	 * Drop Index
	 * @return [type] [description]
	 */
	public function postDropIndex()
	{
		return $this->elastic->index->drop( Request::get('index') );
	}


	/**
	 * Activate Index
	 * @return [type] [description]
	 */
	public function postActivateIndex()
	{
		$currentIndex = Request::get('current_index');
		$newIndex = Request::get('new_index');
		$alias = config('elastic.index.alias');

		return Bus::dispatch( new ActivateIndex($currentIndex, $newIndex, $alias) );
	}


	/**
	 * Add Index
	 * @return [type] [description]
	 */
	public function postAddIndex()
	{
		return $this->elastic->index->add( Request::get('index') );
	}


	/**
	 * Run the mapping
	 * 
	 * @return array
	 */
	public function postMapIndex()
	{
		$index = Request::get('index');
		$type = Request::get('type');

		return Bus::dispatch( new RunMapping($index, $type) );
	}


	/**
	 * Post Index Info
	 * 
	 * @return json
	 */
	public function postIndexData()
	{
		$index = Request::get('index');

		return json_encode($this->indexData($index));

	}


	public function postStartIndexing()
	{
		$index = Request::get('index');
		$type = Request::get('type');
		$from = Request::get('from');
		$take = Request::get('take');

		return Bus::dispatch( new RunIndexing($index, $type, $from, $take) );

	}


	

}