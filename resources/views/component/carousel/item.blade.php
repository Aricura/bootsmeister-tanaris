<div class="item {{ $class }}">
	<img src="{{ asset($slide['src']) }}" alt="{{ $slide['caption'] }}">
	@if (\Illuminate\Support\Str::length($slide['caption']) > 0)
		<div class="carousel-caption">
			<h2>{{ $slide['caption'] }}</h2>
			@if(\Illuminate\Support\Str::length($slide['body']) > 0)
				<p>{{ $slide['body'] }}</p>
			@endif
		</div>
	@endif
</div>