<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta property="og:url" content="{{env('APP_URL')}}">
	<meta property="og:type" content="website">
	<meta property="og:title" content="@lang('common.app-title')">
	<meta property="og:description" content="Wypróbuj naszą platformę demo! Wejdź na demo.wiecejnizlek.pl i sprawdź, co czeka Cię na kursie!">
	<meta property="og:image" content="https://wiecejnizlek.pl/wp-content/themes/wiecejnizlek/assets/fb_og_mainpage.png">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@lang('common.app-title')</title>

	<link rel="icon" href="{{ url('favicon.png') }}">

	<!-- Styles -->
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">
	<link href="{{ mix('css/emoji.css') }}" rel="stylesheet">

	<!-- Scripts -->
	<script src="https://use.fontawesome.com/c95376cac6.js" async></script>
	<script src="https://use.typekit.net/hal1etr.js" async></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>
	<script>
		window.Laravel = <?php echo json_encode([
			'csrfToken' => csrf_token(),
		]); ?>
	</script>
	@include('tracking')
</head>
<body data-base="{{env('APP_URL')}}">
	<div id="app" data-view="@yield('current-view')" class="full-height"></div>

	<form method="post" action="/logout" id="logout-form">
		{{ csrf_field() }}
	</form>
	@include ('footer')
</body>
</html>
