<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ config('app.name') }}</title>

	<link rel="shortcut icon" type="image/png" href="#">

	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script type="text/javascript">window.Laravel = {"csrfToken": "{{ csrf_token() }}"};</script>

	<link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">

</head>
<body>
	@include('header')
	@yield('body')
</body>

<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</html>