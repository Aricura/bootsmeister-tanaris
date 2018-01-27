<div class="thumbnail progress-tile">
	{{-- Raid teaser image within the /images/raids/ folder --}}
	<img src="{{ asset('/images/raids/' . \Illuminate\Support\Str::slug($raid) . '-small.jpg') }}" alt="{{ $raid }}">

	{{-- Progress caption --}}
	<div class="caption">
		<h3>{{ $raid }}</h3>
		<ul class="list-unstyled list-inline">

			{{-- Normal mode --}}
			<li>
				@include('component.progress.caption', ['mode' => 'normal', 'bosses' => $bosses, 'kills' => $nm])
			</li>

			{{-- Heroic mode --}}
			<li>
				@include('component.progress.caption', ['mode' => 'heroic', 'bosses' => $bosses, 'kills' => $hm])
			</li>

			{{-- Mythic mode --}}
			<li>
				@include('component.progress.caption', ['mode' => 'mythic', 'bosses' => $bosses, 'kills' => $mm])
			</li>
		</ul>
	</div>
</div>