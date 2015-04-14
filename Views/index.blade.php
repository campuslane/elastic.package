@extends('elastic::layouts.default')


@section('page_title', 'Index Info')


@section('content')

	<ol class="breadcrumb">
		<li><a href="/{{config('elastic.route')}}">Indexes</a></li>
		<li class="active">{{$name}}</li>
	</ol>

	<h1>{!!($active) ? '<i style="color:#18BC9C;" class="fa fa-circle"></i>' : ''!!} {{strtoupper($name)}} <span style="color:#ccc">INDEX</span></h1>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Map Index</h4>
      </div>
      <div class="modal-body">
        When you're ready to map the index click the button below
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default modal-close-button" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary modal-submit-button">Map Index</button>
      </div>
    </div>
  </div>
</div>


	<hr>
	
	<a class="map-index-link" href="#">Map</a> | 
	<a class="index-index-link" href="">Index</a> | 
	<!-- <a href="">Update Settings</a> |  -->
	<a class="drop-index-link" href="#">Drop</a> | 
	

	@if($state == 'open')
		<a href="">Close</a> | 
	@else
		<a href="">Open</a> |
	@endif

	
	<a href="">Recycle</a> <hr>

	<div class="index-table">Loading Index Table</div>


@stop

@section('scripts')

<script>

/**
 * Reset the modal title, content, buttons, etc.
 */
function resetModal() {

	$('.close, .modal-close-button').removeClass('reload');
	$('.modal-title').html('');
	$('.modal-body').html('');
	$('.modal-submit-button').show();

}

function getIndexInfo() {

	var index = '{{$name}}';

	$.ajax({
		type: 'post', 
		url: '/{{config("elastic.route")}}/index-data', 
		data: {index: index, _token: '{{Session::token()}}'},
		dataType: 'json', 
		success: function(response) {

			var table = '<table class="table table-bordered">'
			table += '<tr><th>Index Name</th><td>' + response.name + '</td></tr>';
			table += '<tr><th>Types</th><td>';
				$.map(response.types, function(type) {
					table += type +  ' (';
					table += '<a class="map-type" data-type="' + type + '"';
					table += ' href="#">map</a> | ';
					table += '<a class="index-type" data-type="' + type + '"'
					table += ' href="#">index</a>';
					table += ')<br>';
				});
			table +='</td></tr>';
			table += '<tr><th>Created</th><td>' + response.creation_date + '</td></tr>';
			table += '<tr><th>State</th><td>' + response.state + '</td></tr>';
			table += '<tr><th>Status</th><td>' + response.active + '</td></tr>';
			table += '<tr><th>Aliases</th><td>' + response.aliases + '</td></tr>';
			table += '<tr><th>Docs</th><td>' + response.docs + '</td></tr>';
			table += '<tr><th>JSON</th><td><pre>' + response.json + '</pre></td></tr>';
			table += '</table>';

			$('div.index-table').html(table);
	
		}
	});
}


/**
 * Do the mapping
 */
function mapIndex(type) {

	var type = (typeof type !== "undefined") ? type : '';

	$('.modal-body').html('<i class="fa fa-spinner fa-spin"></i> The index is being mapped....');

	var index = '{{$name}}';

	$.ajax({
		type: 'post', 
		url: '/{{config("elastic.route")}}/map-index', 
		data: {index: index, type: type, _token: '{{Session::token()}}'}, 
		dataType: 'json', 
		success: function(response) {

			console.log(response);

			var $modalBody = $('.modal-body');

			if( typeof response.acknowledged !== "undefined" && response.acknowledged === true ) {
				$('.modal-submit-button').hide();
				$modalBody.html('Done!  The Index was Mapped');
				getIndexInfo();
			} else {
				$modalBody.html('Oops, there was a problem mapping');
			}

			
			
		}
	});
}


/**
 * Do the indexing
 */
function startIndexing(type, from, take) {

	var from = (typeof from !== "undefined") ? from : 0;
	var take = (typeof take !== "undefined") ? take : {{config("elastic.indexing.take")}};

	var index = '{{$name}}';

	$.ajax({
		type: 'post', 
		url: '/{{config("elastic.route")}}/start-indexing', 
		data: {index: index, type: type, from:from, take:take, _token: '{{Session::token()}}'}, 
		dataType: 'json', 
		success: function(response) {

			var percentageComplete = (parseInt(response.from) / parseInt(response.total) ) * 100;

			$('.progress-bar').css('width', percentageComplete + '%');

			 $('.modal-body > .indexing-status').html( response.from + ' records indexed...');

			 if( response.remaining == 0 || typeof response.remaining == 'undefined') {

			 	getIndexInfo();
			 	$('.modal-body').append('<div>Finished Indexing...</div>');
			 	$('.modal-submit-button').hide();

			 	return false;
			 }

			startIndexing( type, response.from );

		}
	});
}


/**
 * Drop the index
 */
function dropIndex() {

	$('.modal-body').html('<i class="fa fa-spinner fa-spin"></i> The index is being dropped....');

	var index = '{{$name}}';

	$.ajax({
		type: 'post', 
		url: '/{{config("elastic.route")}}/drop-index', 
		data: {index: index, _token: '{{Session::token()}}'}, 
		dataType: 'json', 
		success: function(response) {

			console.log(response);

			var $modalBody = $('.modal-body');

			if( typeof response.acknowledged !== "undefined" && response.acknowledged === true ) {
				$('.modal-submit-button').hide();
				$modalBody.html('The index <strong>{{$name}}</strong> was dropped....');
				location.href = '/{{config("elastic.route")}}';
			} else {
				$modalBody.html('Oops, there was a problem dropping the index:<strong>{{$name}}</strong>');
			}

			
			
		}
	});

}







$(document).ready(function() {

	getIndexInfo();


	


	$(document).on('click', '.index-type', function(e){

		e.preventDefault();

		var type = $(this).attr('data-type');

		resetModal();

		$('.modal-title').html('Indexing');
		$('.modal-body').html('Press the Start Indexing button below to start the indexing process for the type: ' + type + '. <br><br>');

		var progress = '<div class="progress" style="display:none">';
		progress += '<div  class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"';
		progress += ' aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">';
		progress += '</div></div>';

		$('.modal-body').append(progress).append('<div class="indexing-status"></div>');

		$('.modal-submit-button').attr('data-action', 'start-indexing').attr('data-type', type).html('Start Indexing').show();
		$('#myModal').modal('show');

	});


	// deprecated
	// $(document).on('click', '.map-index-link', function(e) {

	// 	e.preventDefault();

	// 	resetModal();

	// 	$('.modal-title').html('Map Index');
	// 	$('.modal-body').html('Click the Map Index button to start the mapping.');
	// 	$('.modal-submit-button').attr('data-action', 'map-index').html('Map Index').show();
	// 	$('#myModal').modal('show');

	// });

	$(document).on('click', '.map-type', function(e){

		e.preventDefault();

		var type = $(this).attr('data-type');

		resetModal();

		$('.modal-title').html('Map Index');
		$('.modal-body').html('Click the Map Index button to start the mapping for the type: ' + type);
		$('.modal-submit-button').attr('data-action', 'map-index').attr('data-type', type).html('Map Index').show();
		$('#myModal').modal('show');
		
	});



	$(document).on('click', '.drop-index-link', function(e) {

		e.preventDefault();

		resetModal();

		$('.modal-title').html('Drop Index');
		$('.modal-body').html('Are you sure you want to drop the: <strong>{{$name}}</strong> index?');
		$('.modal-submit-button').attr('data-action', 'drop-index').html('Drop Index').show();
		$('#myModal').modal('show');

	});

	// deprecated
	$(document).on('click', '.index-index-link', function(e) {

		e.preventDefault();

		resetModal();

		$('.modal-title').html('Indexing');
		$('.modal-body').html('Press the Start Indexing button below to start the indexing process. <br><br>');

		var progress = '<div class="progress" style="display:none">';
		progress += '<div  class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"';
		progress += ' aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">';
		progress += '</div></div>';

		$('.modal-body').append(progress).append('<div class="indexing-status"></div>');

		$('.modal-submit-button').attr('data-action', 'start-indexing').html('Start Indexing').show();
		$('#myModal').modal('show');

	});

	$(document).on('click', '.modal-submit-button', function(e){

		e.preventDefault();

		var action = $(this).attr('data-action');


		if (action == 'map-index') {

			var type = $(this).attr('data-type');
			mapIndex(type);

		} else if (action == 'drop-index') {

			dropIndex();

		} else if (action == 'start-indexing') {

			var type = $(this).attr('data-type');
			$('.progress').show();
			startIndexing(type, 0);

		}

	});

});

</script>
@stop