<!doctype html>
<html lang="ru">
<head>﻿
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
	
	<!-- https://feathericons.com -->
	<script src="{{ asset('js/feather.min.js') }}"></script>
		
	<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,400i,600,600i,700&display=swap" rel="stylesheet">
    <title>@yield('title') | Доктор Вет</title>
	
	<!-- https://www.frescojs.com -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/fresco.css') }}">
	<script src="{{ asset('js/fresco.min.js') }}"></script>
	    		
	<script src="{{ asset('js/toastr.js') }}"></script>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.css') }}">
	
	<!-- https://select2.org/ -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
	<script src="{{ asset('js/select2.min.js') }}"></script>
	
	<!-- Select2 bootstrap themes https://github.com/apalfrey/select2-bootstrap-5-theme -->
	<link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" />
	
	<!-- Select2 changed styles -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min_add.css') }}">
		
	<!-- https://dadata.ru  https://dadata.ru/suggestions/usage/address/ -->
	<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/js/jquery.suggestions.min.js"></script>
	
	<!-- http://t1m0n.name/air-datepicker/docs/index.html -->
	<!-- https://air-datepicker.com/ru - новая версия !!! -->	
	<link href="{{ asset('css/datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/datepicker.min.js') }}"></script>
	
	<!-- https://xdsoft.net/jodit/ -->
	<link href="{{ asset('css/jodit.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/jodit.min.js') }}"></script>
	
	<!-- https://www.chartjs.org -->
    <script src="{{ asset('js/chart.min.js') }}"></script>
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

</head>
<body class="d-flex align-items-start mycontainer bg-white">

	<style>
		.mycontainer {
			min-width: 480px;
		}
	</style>

	<?php  
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1
	?>
    
	@include('navbar')
    <main name="main" id="main" class="container-fluid mycontainer my-3">
        @yield('content')
    </main>
    @include('footer')
	
</body>

<script>

	// Установка отступов
	$('body').css('margin-top', $('.navbar').outerHeight() + 'px');
	$('body').css('margin-bottom', $('.footer').outerHeight() + 'px');
	
	// Установка высоты контента
	var minheight = $(window).height() - $('#navbar').outerHeight() - $('#footer').outerHeight() - parseInt($('#main').css("marginTop").replace('px', ''))*2;
	document.getElementById("central_area").style.minHeight = minheight +"px";
	
	// Задержка показа контента, чтобы загружалось плавнее
	setTimeout(function(){
	  $('#central_area').removeClass('d-none');
	}, 100);
		
</script>



</html>