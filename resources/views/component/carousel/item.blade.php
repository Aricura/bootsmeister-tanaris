<div class="item @if($first) active @endif">
	{{-- Image within the /images/backgorund/ folder --}}
	<img src="{{ asset('/images/background/'.$slide['src']) }}" alt="{{ $slide['caption'] }}">

	{{-- Optional caption --}}
	@if ('' !== $slide['caption'])
		<div class="carousel-caption">
			{{-- H1 for the first slide --}}
			@if($first)
				<h1>{{ $slide['caption'] }}</h1>
			@else
				<h2 class="h1">{{ $slide['caption'] }}</h2>
			@endif

			{{-- Optional body text --}}
			@if('' !== $slide['body'])
				<p>{{ $slide['body'] }}</p>
			@endif
		</div>
	@endif
</div>