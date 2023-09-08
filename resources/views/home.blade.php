@extends('layouts.app')
@section('content')
    <div>
        @guest
            <h1 class="display-4 text-center text-primary">Home BoolBNB Guest</h1>
        @endguest
        @auth
            <h1 class="display-4 text-center text-primary">Home BoolBNB {{ Auth::user()->name }}</h1>
        @endauth

        <div class="text-center my-3">
            <a href="{{route('create')}}" class="btn btn-success">CREATE</a>
        </div>

        <div class="row">
            @php
                // Ordina gli appartamenti in base al campo sponsor (true prima di false)
                $sorted_apartments = $apartments->sortByDesc('sponsor');
            @endphp
            @foreach ($sorted_apartments as $apartment)
                <div class="col-sm-4 my-3">
                    <div class="card shadow @if ($apartment->sponsor) bg-info  @endif">
                        <div class="card-body">
                            <h5 class="card-title">{{ $apartment->name }}</h5>
                            <p class="card-text">{{ $apartment->address }}</p>
                            <p class="card-text">Metri quadrati: {{ $apartment->mq }}</p>
                            <a href="{{ route('show', $apartment->id) }}" class="btn btn-primary">Visualizza</a>
                            @if ($apartment ->sponsor)
                                <span class="btn btn-warning">Sponsorizzato</span>
                            @endif
                            

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection