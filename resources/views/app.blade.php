<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ config('app.name') }}</title>

	<link rel="shortcut icon" type="image/png" href="{{ asset('images/brand/bootsmeister-tanaris-fav.png') }}">

	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script type="text/javascript">window.Laravel = {"csrfToken": "{{ csrf_token() }}"};</script>

	<link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">

</head>
<body>
	<main>
		@include('one-pager.carousel')
		@include('one-pager.header')
		@include('one-pager.wheel')
		@include('one-pager.progress')
		@include('one-pager.team')
		@include('one-pager.recruitment')
		@include('one-pager.footer')
		@include('one-pager.imprint')
	</main>
</body>

<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</html>