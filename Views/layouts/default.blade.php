<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">

	<title>@yield('page_title')</title>

	{!! Html::style('css/elastic/elastic.css') !!}
	
</head>

<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Administration</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      
     
        

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>



	<div class="container">
		@yield('content')
 	</div>

	{!! Html::script('js/elastic/elastic.js') !!}
	
      @yield('scripts');


</body>
</html>