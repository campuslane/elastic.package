<?php

return [

	"route" => 'elastic/admin', 

	"index" 	=> [
		"label" => 'App',
		"alias" => 'app'
	], 

	

	"settings" => [
		"class" 	=> 'App\Services\Elastic\Settings',
		"method" => 'update'
	], 


	"mapping" => [
		"class" 	=> 'App\Services\Elastic\Mapping',
		"method" =>'map'
	], 

	"indexing" => [
		"class" 	=> 'App\Services\Elastic\Indexing',
		"method" =>'index'
	], 


];