@extends('layouts.app')
@section('content')
    <div>

        <h1 class="display-4 text-center text-primary">Home BoolBNB</h1>
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