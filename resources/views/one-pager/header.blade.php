<header class="navbar navbar-inverse">
	<div class="container">

		<div class="navbar-header">
			{{-- Collapsed burger --}}
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-nav" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			{{-- Brand image and name --}}
			<a class="navbar-brand" href="{{ route('home') }}">
				<img src="{{ asset('images/brand/bootsmeister-tanaris.png') }}" alt="{{ config('app.name') }}">
				{{ config('app.name') }}
			</a>
		</div>


		<div class="collapse navbar-collapse" id="header-nav">
			{{-- Navigation right --}}
			<ul class="nav navbar-nav navbar-right">
				<li><a href="javascript:void(0);">Progress</a></li>
				<li><a href="javascript:void(0);">Team</a></li>
				<li><a href="javascript:void(0);">Recruitment</a></li>
			</ul>
		</div>
	</div>
</header>