<div id="carousel{{ $id }}" class="carousel slide" data-ride="carousel">

	{{-- Indicators --}}
	@if (count($slides) > 1)
		<ol class="carousel-indicators">
			@foreach($slides as $slide)
				<li data-target="#carousel{{ $id }}" data-slide-to="{{ $loop->index }}" @if($loop->first)class="active"@endif></li>
			@endforeach
		</ol>
	@endif


	{{-- Wrapper for slides --}}
	<div class="carousel-inner" role="listbox">
		@foreach($slides as $slide)
			@include('component.carousel.item', [
			'class' => $loop->first ? 'active' : '',
			'slide' => $slide
			])
		@endforeach
	</div>

	{{-- Controls --}}
	@if (count($slides) > 1)
		<a class="left carousel-control" href="#carousel{{ $id }}" role="button" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#carousel{{ $id }}" role="button" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	@endif
</div>