{{-- The guild leader will be the first active slider (guild rank = 0) --}}
<div class="item @if($active) active @endif" id="team-carousel--{{ $raidMember->guild_member_id }}">

	{{-- Character image --}}
	<img src="{{ $raidMember->GuildMember->getProfilePicture() }}" alt="{{ $raidMember->GuildMember->name }}" class="img-responsive">


	{{-- Top-Left caption --}}
	<div class="carousel-caption carousel-caption--top-left">
		{{-- Character name --}}
		<h3 class="h1">
			{{ $raidMember->GuildMember->name }}

			{{-- check for guild leader --}}
			@if($raidMember->GuildMember->guild_rank === 0)
				<img src="{{ asset('/images/icons/guild-master.png') }}" alt="Guild Master">
			@elseif($raidMember->GuildMember->guild_rank === 1)
				<img src="{{ asset('/images/icons/officer.png') }}" alt="Guild Officer">
			@endif
		</h3>

		{{-- Spec and class name --}}
		<p class="class-color {{ $raidMember->GuildMember->CharacterClass->getCssClassNameAttribute() }}">
			{{ $raidMember->Spec->name }} {{ $raidMember->GuildMember->CharacterClass->name }}
		</p>
	</div>

	{{-- Bottom-Left caption --}}
	<div class="carousel-caption carousel-caption--bottom-left">
		{{-- Item level --}}
		<p>
			<small class="small">Item Level</small>
			<strong>{{ number_format(doubleval($raidMember->GuildMember->item_level_equipped), 1, ",", ".") }}</strong>
		</p>
	</div>


	{{-- Bottom-Right caption --}}
	<div class="carousel-caption carousel-caption--bottom-right">
		{{-- External links --}}
		<ul class="list-unstyled">
			<li>
				{{-- Armory --}}
				<a href="{{ $raidMember->GuildMember->getArmoryLink() }}" target="_blank" role="button" title="WOW Armory">
					<img src="{{ asset('/images/icons/wow-white.png') }}" alt="WWW Armory of {{ $raidMember->GuildMember->name }}">
				</a>
			</li>
			<li>
				{{-- WOW Progress --}}
				<a href="{{ $raidMember->GuildMember->getWoWProgressLink() }}" target="_blank" role="button" title="wowprogress.com">
					Progress
				</a>
			</li>
		</ul>
	</div>
</div>