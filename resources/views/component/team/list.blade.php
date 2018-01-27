<ul class="list-group team-list">

	{{-- List all members of this role --}}
	@foreach($raidMembers as $raidMember)
		<li class="list-group-item" onclick="jQuery('#team-carousel').carousel(jQuery('#team-carousel--{{ $raidMember->guild_member_id }}').index());">
			<img class="class-icon" src="{{ asset($raidMember->GuildMember->CharacterClass->getIconUrl()) }}" alt="{{ $raidMember->GuildMember->CharacterClass->name }}">
			<img class="spec-icon" src="{{ asset($raidMember->Spec->getIconUrl()) }}" alt="{{ $raidMember->Spec->name }}">

			<small class="{{ $raidMember->GuildMember->CharacterClass->getCssClassNameAttribute() }}">
				{{ $raidMember->GuildMember->name }}
			</small>
		</li>
	@endforeach

</ul>