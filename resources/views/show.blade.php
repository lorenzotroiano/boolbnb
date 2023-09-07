@extends('layouts.app')
@section('content')
    <h1>Questa è la show di <b>{{ $apartment->name }}</b> </h1>

    {{-- Descrizione --}}
    <p>
        <b>Description</b> {{ $apartment->description }}
    </p>

    <span>Room : {{ $apartment->room }}</span><br>
    <span>Bathroom : {{ $apartment->bathroom }}</span><br>
    <span>mq : {{ $apartment->mq }}</span><br>
    <span>address : {{ $apartment->address }}</span><br>
    <span>latitude : {{ $apartment->latitude }}</span><br>
    <span>longitude : {{ $apartment->longitude }}</span><br>
    <div>
        @if ($apartment->cover)
            <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px" alt="Immagine di Mauro">
        @else
            Immagine non disponibile
        @endif
    </div>
@endsection
