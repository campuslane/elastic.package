@extends('elastic::layouts.default')

@section('page_title', 'ElasticSearch')


@section('content')

<div class="row">
<div class="col-lg-12">

	<h1>ElasticSearch</h1>

	<br>

	<h3>Indexes</h3>

	<table class="table table-bordered">
		<tr>
			
			<th style="text-align:center">Active</th>
			<th>Index</th>
			<th>Created</th>
			<th>Docs</th>
			<th>Alias</th>
			<th>Size</th>
			<th>Actions</th>
			
		</tr>

		@foreach($indexes as $index)

			<tr>
				<td style="text-align:center">
					@if($activeIndex == $index['name'])
						<span style="color:#18BC9C;" class="primary-color fa fa-circle"></span>
					@else
						<span  data-index-name="{{$index['name']}}" style="color:#ccc; cursor:pointer;" class="fa fa-circle-o activate-index"></span>
						<i style="display:none" class="fa fa-spinner fa-spin activate-spinner"></i>
					@endif
				</td>

				<td>
					{{$index['name']}}
				</td>

				<td>
					{{Carbon\Carbon::createFromTimeStamp($index['settings']['creation_date'])->toDateTimeString()}}
				</td>

				<td>{{$index['docs']}}</td>
				<td>{{$index['aliases']}}</td>

				<td>{{$index['size']}}</td>

				<td data-index-name="{{$index['name']}}">	
					

					<a class="start-indexing"  href="#">Set</a>  
					<i style="display:none" class="fa fa-spinner fa-spin index-spinner"></i>  |

					<a class="map-index"  href="#">Map</a>  
					<i style="display:none" class="fa fa-spinner fa-spin map-spinner"></i>  | 

					<a class="start-indexing"  href="#">Index</a>  
					<i style="display:none" class="fa fa-spinner fa-spin index-spinner"></i>  |

					<a class="drop-index"  href="#">Close</a>  
					<i style="display:none" class="fa fa-spinner fa-spin drop-spinner"></i>  | 

					<a class="drop-index"  href="#">Drop</a>  
					<i style="display:none" class="fa fa-spinner fa-spin drop-spinner"></i>  

					
				</td>
			</tr>
		@endforeach

	</table>


</div>
</div>

<div class="row">
<div class="col-lg-6 col-md-6 col-sm-6 ">

	<a href="#" class="add-index-container-button">+ New Index</a> <br><br>

	<div style="display:none" class="add-index-container well">
		<div class="error"></div>
		<form class="form-inline add-index">
		  <div class="form-group">
		    <input type="text" class="form-control input-sm index-name"  placeholder="Index Name">
		  </div>
		  <button type="submit" class="btn btn-primary btn-sm add-index-button">Create Index</button> 
		  <i style="display:none" class="fa fa-spinner fa-spin new-index-spinner"></i>
		  

		  
		</form>
	</div>
</div>

</div>







<br>
<br>
<br>
<br>
<br>

@stop


@section('scripts')

<script>

$(document).ready(function() {

	$(document).on('click', '.add-index-container-button', function(e){
		e.preventDefault();

		$('.add-index-container').toggle();

	});

	$(document).on('click', '.add-alias-container-button', function(e){
		e.preventDefault();

		$('.add-alias-container').toggle();

	});

	$(document).on('click', '.drop-alias', function(e){

		e.preventDefault();

		var alias = $(this).attr('data-alias');
		var index = $(this).attr('data-index');

		$.ajax({
			type: 'post', 
			url: '/es/delete-alias', 
			data: {alias:alias, index:index, _token: '{{Session::token()}}'}, 
			dataType: 'json', 
			success: function(response) {

				if (typeof response.error == 'undefined') {
					window.location.href = '/{{config("elastic.route")}}';
				} else {
					$('.error').html(response.error);
				}
			}
		});

	});

	$(document).on('click', '.drop-index', function(e) {

		e.preventDefault();

		$(this).next('.drop-spinner').show();

		var index = $(this).parent().attr('data-index-name');

		$.ajax({
			type: 'post', 
			url: '/es/drop-index', 
			data: {index:index, _token: '{{Session::token()}}'}, 
			success: function(response) {
				window.location.href = '/{{config("elastic.route")}}';
			}
		});
	});

	$(document).on('click', '.add-index-button', function(e) {

		$('form.add-index').trigger('submit');

	});

	$(document).on('submit', 'form.add-index', function(e){

		e.preventDefault();

		$('.new-index-spinner', this).show();

		var index = $('.index-name').val();

		$.ajax({
			type: 'post', 
			url: '/es/add-index', 
			data: {index:index, _token: '{{Session::token()}}'}, 
			dataType: 'json', 
			success: function(response) {

				if (typeof response.error == 'undefined') {
					window.location.href = '/{{config("elastic.route")}}';
				} else {
					$('.error').html(response.error);
				}

			}
		});
	})

	$(document).on('click', '.activate-index', function(e) {

		e.preventDefault();

		$(this).next('.activate-spinner').show();

		var current_index = '{{$activeIndex}}';
		var new_index = $(this).attr('data-index-name');

		$.ajax({
			type: 'post', 
			url: '/es/activate-index', 
			data: {current_index:current_index, new_index:new_index, _token: '{{Session::token()}}'}, 
			dataType: 'json', 
			success: function(response) {
				window.location.href = '/{{config("elastic.route")}}';
			}
		});
	});

		



});

</script>

@stop