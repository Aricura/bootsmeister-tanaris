<section class="module module--wheel">

	<h2>World of Warcraft Gilde</h2>
	<hr/>

	<div class="infographic infographic--default infographic--large" id="infographic">

		{{-- Wheel --}}
		<div class="infographic__wheel">
			<div class="wheel wheel--default wheel--{{ count($wheelItems) }} js-wheel">
				@foreach($wheelItems as $item)
					{{-- Add a dot for each wheel item --}}
					<div class="wheel__spoke">
						<button class="dot dot--default js-wheel-dot">
							{{ $item['title'] }}
						</button>
					</div>
				@endforeach

				{{-- Logo --}}
				<div class="wheel__image is-active js-wheel-image">
					<div class="image image--circle">
						<img src="{{ asset('images/brand/bootsmeister-tanaris.png') }}" alt="{{ config('app.name') }}">
					</div>
				</div>

				{{-- Circle --}}
				<svg class="embed embed--image" viewBox="0 0 400 400">
					<circle class="wheel__circle" vector-effect="non-scaling-stroke" cx="200" cy="200" r="198"></circle>
				</svg>
			</div>
		</div>

		{{-- Content --}}
		<div class="infographic__content">
			@foreach($wheelItems as $item)
				{{-- Add the content for each wheel item --}}
				<div class="panel panel--default js-infographic-panel">
					<h4>{{ $item['title'] }}</h4>
					<div class="content">
						{!! $item['body'] !!}
					</div>
				</div>
			@endforeach
		</div>

	</div>

</section>