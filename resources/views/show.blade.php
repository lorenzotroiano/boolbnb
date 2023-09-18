@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Titolo --}}
            <h1 class="mb-4 text-center"><b style="text-transform: capitalize">{{ $apartment->name }}</b></h1>

            <div class="image-container text-center">
                @if ($apartment->cover)
                    <img src="{{ asset('storage/' . $apartment->cover) }}" class="img-fluid rounded mb-4" alt="Apartment Image">
                @else
                    <div class="alert alert-warning">Iage not available</div>
                @endif
            </div>


            {{-- Caratteristiche --}}
            <h3>Character</h3>
            <ul class="list-group mb-4">
                <li class="list-group-item">Description: {{ $apartment->description }}</li>
                <li class="list-group-item">Room: {{ $apartment->room }}</li>
                <li class="list-group-item">Bathroom: {{ $apartment->bathroom }}</li>
                <li class="list-group-item">mq: {{ $apartment->mq }}</li>
                <li class="list-group-item">Address: {{ $apartment->address }}</li>
                <li class="list-group-item">Latitude: {{ $apartment->latitude }}</li>
                <li class="list-group-item">Longitude: {{ $apartment->longitude }}</li>
            </ul>

            {{-- Servizi --}}
            <h4 class="mb-3">Services:</h4>
            <ul class="list-group mb-4">
                @foreach ($apartment->services as $service)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $service->name }}
                        <span class="badge badge-primary badge-pill">{{ $service->icon }}</span>
                    </li>
                @endforeach
            </ul>

            

        @foreach($apartment->messages as $message)
            
            <p><strong>Nome:</strong> {{ $message->name }}</p>

            
            <p><strong>Messaggio:</strong> {{ $message->body }}</p>

            
            <p><strong>Da:</strong> {{ $message->email }}</p>

            
            <hr>
        @endforeach


            {{-- Numero di visualizzazioni --}}
            @if(Auth::check() && $apartment->user_id === Auth::id())
                <div class="alert alert-info mt-4">
                    <b>Number of visualizations:</b> {{ $apartment->views->count() }}
                </div>
            @endif


            {{-- Tempo mancante sponsorizzazioni --}}
            @if(Auth::check() && $apartment->sponsor === 1)
            <div class="alert alert-info mt-4">
                @foreach ($apartmentsponsors as $apartmentsponsor)
                    @if ($apartmentsponsor->apartment_id === $apartment->id)
                        @php
                            // Converto in formato data
                            $startDate = \Carbon\Carbon::parse($apartmentsponsor->start_date);
                            $endDate = \Carbon\Carbon::parse($apartmentsponsor->end_date);
                            $hoursDifference = $endDate->diffInHours($startDate);
                        @endphp
                        <div class="alert alert-info mt-4">
                            <b>Time left for sponsorship:</b> {{ $hoursDifference }} hours
                        </div>                        
                    @endif
                @endforeach
            </div>
        @endif
        
        

            {{-- Delete e Modify --}}
            @if (Auth::id() === $apartment->user_id)

                {{-- MODIFY --}}
                <a href="{{ route('edit', $apartment->id) }}" class="btn btn-warning mb-2">MODIFY APARTMENT</a>

                {{-- DELETE --}}
                <form class="d-inline" method="POST" action="{{ route('delete', $apartment->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mb-2">DELETE</button>
                </form>

                {{-- DELETE PICTURE --}}
                <form class="d-inline" method="POST" action="{{ route('apartment.picture.delete', $apartment->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mb-2">DELETE PICTURE</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
