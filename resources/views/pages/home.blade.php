@extends('main')


@section('body')
	@include('content.carousel.full-screen', ['slides' => $slides, 'arrow_down' => true])


	<section class="module module--text">
		<div class="container">
			<div class="row">
				<div class="col-md-6 mb40">
					<h1>{{ config('app.name') }}</h1>
					<hr/>
					<p>
						Gedacht als einmaliger "running Gag" hat sich die Geschichte des Bootsmeisters in Tanaris immer weiterentwickelt.
					</p>
					<p>
						Du bist ein Twink, frisch auf Level 110 und fragst dich, wie du als Hexer deinen Doomguard bekommst?
						Geh nach Tanaris, suche den versteckten nur zu bestimmten Uhrzeiten spawnden Bootsmeister, erledige seine Quests, löse die drei Aufgaben der drei fürchterlichen Piraten, besiege den Schwertmeister, stehle das Idol des Gouverneurs und finde den Schatz. Der Kopf des Navigators wird dich leiten.
					</p>
					<p>
						Du bekommst einen Whisper ohne Hallo und nur ein: "woher Skin" hingeklatscht? Weise ihn freundlich auf die Questreihe des Bootsmeisters hin. All dies lässt sich unendlich weiterführen. Und ja, es klappt wirklich ;)
						Abgesehen davon wären alle anderen Namensvorschläge gegen die Term's of Use gewesen.
					</p>
				</div>

				<div class="col-md-6">

				</div>
			</div>
		</div>
	</section>


	@if($guildRank !== null && property_exists($guildRank, 'world_rank') && property_exists($guildRank, 'area_rank') && property_exists($guildRank, 'realm_rank'))
		<section class="module module--background module--parallax" style="background-image: url('{{ asset('images/background/tanaris.jpg') }}')">
			<div class="pt40 pb40 overlay">
				<p class="text-center h1 white">Tier 21</p>
				<hr class="center"/>
				<p class="text-center h2 white">
					Realm {{ number_format($guildRank->realm_rank, 0, ',', '.') }}
					<span class="ml25 mr25">&nbsp;</span>
					German {{ number_format($guildRank->area_rank, 0, ',', '.') }}
					<span class="ml25 mr25">&nbsp;</span>
					World {{ number_format($guildRank->world_rank, 0, ',', '.') }}
				</p>
				<p class="text-center mb30">
					<a href="https://www.wowprogress.com/guild/eu/thrall/Bootsmeister+Tanaris+eV" target="_blank" class="white">wowprogress.com</a>
				</p>
			</div>
		</section>
	@endif


	<section class="module module--text">
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
@endsection