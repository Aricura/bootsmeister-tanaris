<section class="module module--carousel">
	<div id="carousel" class="carousel slide" data-ride="carousel">

		{{-- Indicators --}}
		@if(false)
			<ol class="carousel-indicators">
				<li data-target="#carousel" data-slide-to="1" class="active"></li>
			</ol>
		@endif


		{{-- Wrapper for slides --}}
		<div class="carousel-inner" role="listbox">

			{{-- Slide 1 --}}
			@include('component.carousel.item', [
				'first' => true,
				'slide' => [
					'src' => 'antorus-heroic-clear.jpg',
					'caption' => config('app.name'),
					'body' => '15.01.18 Antorus Heroic clear.'
				]
			])

		</div>


		{{-- Controls --}}
		@if(false)
			<a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#carousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		@endif
	</div>
</section>