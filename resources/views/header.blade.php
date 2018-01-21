<header>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">

			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-nav" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ route('home') }}">
					<img src="{{ asset('images/brand/bootsmeister-tanaris.png') }}" alt="{{ config('app.name') }}">
					{{ config('app.name') }}
				</a>
			</div>

			<div class="collapse navbar-collapse" id="header-nav">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="{{ route('home') }}" @if(Route::currentRouteName() == 'home') class="active" @endif>Home</a></li>
					<li><a href="{{ route('team') }}" @if(Route::currentRouteName() == 'team') class="active" @endif>Team</a></li>
					<li><a href="{{ route('progress') }}" @if(Route::currentRouteName() == 'progress') class="active" @endif>Progress</a></li>
				</ul>
			</div>
		</div>
	</nav>
</header>