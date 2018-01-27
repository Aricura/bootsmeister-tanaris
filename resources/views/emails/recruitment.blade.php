@extends('emails.template')

@section('content')
	<h1 style="color: #d1534b;">Neue Gildenbewerbung</h1>

	<p><strong>Battle.net Id</strong>: <span>{{ $bnet }}</span></p>
	<p><strong>Armory Link</strong>: <a href="{{ $armory }}">{{ $armory }}</a></p>
	<p><strong>Spezialisierung</strong>: <span>{{ $spec->CharacterClass->name }} / {{ $spec->name }}</span></p>

	<br/>

	<p><strong>Raiderfahrung</strong>: <span>{!! nl2br($exp) !!}</span></p>

	<br/>
	<br/>

	<p style="color: #d1534b;">Schöne Grüße,<br/>Bootsmeister Tanaris eV</p>
@endsection