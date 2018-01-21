<section class="module module--carousel module--fullscreen">
	@include('component.carousel.carousel', ['id' => 1, 'slides' => $slides])

	@if($arrow_down)
		<i class="icon icon--arrow-down glyphicon glyphicon-chevron-down"></i>
	@endif
</section>