<section class="module module--team" id="team">
	<div class="container">

		<h2>User Team</h2>
		<hr/>

		<div class="row">

			{{-- Carousel --}}
			<div class="col-md-8 col-sm-6 hidden-xs">
				<div id="team-carousel" class="carousel slide" data-ride="carousel">
					{{-- Wrapper for all slides --}}
					<div class="carousel-inner" role="listbox">
						{{-- Loop thorugh all roles --}}
						@foreach($raidMemberCollection as $role => $raidMembers)
							{{-- Loop through all raid members of this role --}}
							@foreach($raidMembers as $raidMember)
								@if(null !== $raidMember && null !== $raidMember->GuildMember)
									@include('component.team.carousel-item', ['raidMember' => $raidMember])
								@endif
							@endforeach
						@endforeach
					</div>

					{{-- Controls --}}
					<a class="left carousel-control" href="#team-carousel" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#team-carousel" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div>


			{{-- Listing --}}
			<div class="col-md-4 col-sm-6 col-xs-12">
				{{-- Loop thorugh all roles --}}
				@foreach($raidMemberCollection as $role => $raidMembers)
					@if(null !== $raidMember && null !== $raidMember->GuildMember && null !== $raidMember->Spec)
						@include('component.team.list', ['role' => $role, 'raidMembers' => $raidMembers])
					@endif
				@endforeach
			</div>

		</div>

	</div>
</section>