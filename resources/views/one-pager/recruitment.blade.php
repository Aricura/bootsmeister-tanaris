<section class="module module--recruitment" id="recruitment">
	<div class="container">

		<h2>Recruitment</h2>
		<hr/>

		<div class="row">

			{{-- We are looking for --}}
			<div class="col-md-4">
				<p class="lead">Was bieten wir</p>
				<ul class="list-unstyled">
					<li>3 Raidtage pro Woche (Do/So/Mo) jeweils von 20.00 - 22.30</li>
					<li>Erfahrene Raidleitung</li>
					<li>Aktiv genutztes Teamspeak</li>
					<li>Mythic Keystone Runs auserhalb der Raidzeiten bzw. wenn Clear</li>
				</ul>

				<p class="lead">Was erwarten wir</p>
				<ul class="list-unstyled">
					<li>Spaß und Freude am Spiel und der eigenen Klasse</li>
					<li>Anwesenheit bei 2 von 3 Raidtagen</li>
					<li>Vorbereitung zum Raid (Guide, Food, Flask, Vz, Sockel, Pots, ...)</li>
					<li>Kritikfähigkeit und Humor</li>
				</ul>
			</div>


			{{-- Form --}}
			<div class="col-lg-7 col-lg-offset-1 col-md-8">

				@if(\Session::has('recruitment_successful'))
					<div class="alert alert-success">
						<p>Danke für deine Bewerbung. Wir werden uns Ingame bei dir melden.</p>
					</div>
				@endif


				<form role="form" method="POST" action="{{ url('/recruitment') }}">
					{{ csrf_field() }}

					{{-- battle.net id --}}
					<div class="form-group{{ $errors->has('bnet') ? ' has-error' : '' }}">
						<label class="control-label" for="bnet">Battle.net Id</label>
						<input type="text" class="form-control" name="bnet" id="bnet" value="{{ old('bnet') }}">
						@if ($errors->has('bnet'))
							<span class="help-block"> <strong>{{ $errors->first('bnet') }}</strong></span>
						@endif
					</div>

					{{-- armory --}}
					<div class="form-group{{ $errors->has('armory') ? ' has-error' : '' }}">
						<label class="control-label" for="armory">Armory Link</label>
						<input type="text" class="form-control" name="armory" id="armory" value="{{ old('armory') }}">
						@if ($errors->has('armory'))
							<span class="help-block"> <strong>{{ $errors->first('armory') }}</strong></span>
						@endif
					</div>

					{{-- spec --}}
					<div class="form-group{{ $errors->has('spec') ? ' has-error' : '' }}">
						<label class="control-label" for="spec">Klasse / Spezialisierung (Mainspec)</label>
						<select class="form-control" name="spec" id="spec">
							@foreach(\App\Models\Resources\CharacterClass::all() as $characterClass)
								<optgroup label="{{ $characterClass->name }}">
									@foreach($characterClass->Specs as $characterSpec)
										<option value="{{ $characterSpec->id }}" @if(old('spec') == $characterSpec->id) selected @endif>{{ $characterSpec->name }}</option>
									@endforeach
								</optgroup>
							@endforeach
						</select>
						@if ($errors->has('spec'))
							<span class="help-block"> <strong>{{ $errors->first('spec') }}</strong></span>
						@endif
					</div>

					{{-- raid knowledge --}}
					<div class="form-group{{ $errors->has('exp') ? ' has-error' : '' }}">
						<label class="control-label" for="exp">Erzähl uns etwas über dich (Raiderfahrung, ...)</label>
						<textarea class="form-control" name="exp" id="exp" rows="6">{{ old('exp') }}</textarea>
						@if ($errors->has('exp'))
							<span class="help-block"> <strong>{{ $errors->first('exp') }}</strong></span>
						@endif
					</div>

					{{-- submit button --}}
					<div class="form-group">
						<button type="submit" role="button" class="btn btn-lg btn-primary">Bewerbung senden</button>
					</div>

				</form>
			</div>

		</div>

		<hr/>

		<p class="lead text-center">Alternativ kannst du dich auch ingame melden bei</p>
		<ul class="list-unstyled list-inline text-center">
			<li><strong>Ize#2567</strong></li>
			<li><strong>Xera#2750</strong></li>
			<li><strong>Aricura#2357</strong></li>
		</ul>
	</div>
</section>