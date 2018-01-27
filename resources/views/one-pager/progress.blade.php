{{-- WOWPROGRESS ranking if available --}}
@if(is_array($guildRank) && array_key_exists('world_rank', $guildRank) && array_key_exists('area_rank', $guildRank) && array_key_exists('realm_rank', $guildRank))
	<section class="module module--wow-progress" id="progress">
		<div class="overlay">

			<h2 class="tier text-center">Tier 21</h2>
			<hr/>

			<ul class="list-unstyled list-inline text-center">
				<li>Realm {{ number_format($guildRank['realm_rank'], 0, ',', '.') }}</li>
				<li>German {{ number_format($guildRank['area_rank'], 0, ',', '.') }}</li>
				<li>World {{ number_format($guildRank['world_rank'], 0, ',', '.') }}</li>
			</ul>

			@if(array_key_exists('base_url', $guildRank))
				<p class="text-center">
					<a href="{{ $guildRank['base_url'] }}" target="_blank">wowprogress.com</a>
				</p>
			@endif

		</div>
	</section>
@endif


<section class="module module--progress">
	<div class="container">
		<div class="row">

			<div class="col-md-4">
				@include('component.progress.tile', ['raid' => 'Antorus, the Burning Throne', 'bosses' => 11, 'mm' => 0, 'hm' => 11, 'nm' => 11])
			</div>

			<div class="col-md-4">
				@include('component.progress.tile', ['raid' => 'Tomb of Sargeras', 'bosses' => 9, 'mm' => 0, 'hm' => 9, 'nm' => 9])
			</div>

			<div class="col-md-4">
				@include('component.progress.tile', ['raid' => 'The Nighthold', 'bosses' => 10, 'mm' => 3, 'hm' => 10, 'nm' => 10])
			</div>

		</div>
	</div>
</section>