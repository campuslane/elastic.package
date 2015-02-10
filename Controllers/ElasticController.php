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
	}


	/**
	 * Show the elasticsearch indexes.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$indexes =  Bus::dispatch( new GetIndexesData() );
		$aliases = Bus::dispatch( new GetAliasesData() );
		$activeIndex = Bus::dispatch( new GetActiveIndex( config('elastic.index.alias') ) );
		
		return view('elastic::home', compact('indexes', 'activeIndex'));
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


	

}