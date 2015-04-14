<?php

return [

	"route" => 'elastic/admin', 

	"index" 	=> [
		"label" => 'App',
		"alias" => 'app'
	], 

	

	"settings" => [
		"class" 	=> 'App\Services\Elastic\Settings',
		
	], 


	"mapping" => [
		"class" 	=> 'App\Services\Elastic\Mapping',
		
	], 

	"indexing" => [
		"class" 	=> 'App\Services\Elastic\Indexing',
		"take" 	=> 100, 
	], 


];