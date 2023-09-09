@extends('layouts.app')
@section('content')
    <h1>Questa è la show di <b style="text-transform: capitalize">{{ $apartment->name }}</b> </h1>

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
    @foreach ($apartment->services as $service)
        <div class="form-check mx-auto" style="max-width: 300px">
            <span class="form-check-label">
                {{ $service->name }}
            </span>
            <span class="form-check-label">
                {{ $service->icon }}
            </span>
        </div>
    @endforeach
    <div>
        @if ($apartment->cover)
            <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px" alt="">
        @else
            Immagine non disponibile
        @endif
    </div>

    @if (Auth::id() === $apartment->user_id)
        <span><a href="{{ route('edit', $apartment->id) }}">Modifica appartamento</a></span>
        <form class="d-inline" method="POST" action="{{ route('delete', $apartment->id) }}">

            @csrf
            @method('DELETE')

            <input class="btn btn-primary" type="submit" value="DELETE">
        </form>

        <form class="d-inline" method="POST" action="{{ route('apartment.picture.delete', $apartment->id) }}">
            @csrf
            @method('DELETE')
            <input class="btn btn-primary " type="submit" value="DELETE PICTURE">
        </form>
    @endif

    {{-- MESSAGGIO --}}
    @if(Auth::check())
        <h2>Invia un messaggio</h2>
            <form action="{{ route('send.message', $apartment->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="body">Messaggio:</label>
                    <textarea name="body" id="body" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
    @else
        <h2>Invia un messaggio</h2>
            <form action="{{ route('send.message', $apartment->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nome:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="body">Messaggio:</label>
                    <textarea name="body" id="body" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
    @endif

    
@endsection
