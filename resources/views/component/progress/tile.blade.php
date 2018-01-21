<div class="thumbnail progress-tile">
	<img src="{{ asset('images/raids/' . \Illuminate\Support\Str::slug($raid) . '-small.jpg') }}" alt="{{ $raid }}">
	<div class="caption">
		<h4>{{ $raid }}</h4>
		<ul class="list-unstyled list-inline">
			<li>
				<span class="kills">{{ $nm }}</span>
				<span class="difficulty">normal</span>
				<span class="icon">
					@if ($nm >= $bosses)
						<i class="glyphicon glyphicon-ok"></i>
					@elseif ($nm > 0)
						<i class="glyphicon glyphicon-hourglass"></i>
					@else
						<i class="glyphicon glyphicon-remove"></i>
					@endif
				</span>
			</li>
			<li>
				<span class="kills">{{ $hm }}</span>
				<span class="difficulty">heroic</span>
				<span class="icon">
					@if ($hm >= $bosses)
						<i class="glyphicon glyphicon-ok"></i>
					@elseif ($hm > 0)
						<i class="glyphicon glyphicon-hourglass"></i>
					@else
						<i class="glyphicon glyphicon-remove"></i>
					@endif
				</span>
			</li>
			<li>
				<span class="kills">{{ $mm }}</span>
				<span class="difficulty">mythic</span>
				<span class="icon">
					@if ($mm >= $bosses)
						<i class="glyphicon glyphicon-ok"></i>
					@elseif ($mm > 0)
						<i class="glyphicon glyphicon-hourglass"></i>
					@else
						<i class="glyphicon glyphicon-remove"></i>
					@endif
				</span>
			</li>
		</ul>
	</div>
</div>