@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fs-4 text-secondary my-4">
        {{ __('Dashboard') }}
    </h2>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <h5 class="card-header"> <b>Pubblico</b> </h5>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="row">
                        @php
                            // Ordina gli appartamenti in base al campo sponsor (true prima di false)
                            $sorted_apartments = $apartments->sortByDesc('sponsor');
                        @endphp
                        @foreach ($sorted_apartments as $apartment)
                            @if ($apartment->user_id === Auth::id() && $apartment->visible == 1)
                                <div class="col-sm-4 my-3">
                                    <div class="card shadow @if ($apartment->sponsor) bg-info  @endif">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $apartment->name }}</h5>
                                            <p class="card-text">{{ $apartment->address }}</p>
                                            <p class="card-text">Metri quadrati: {{ $apartment->mq }}</p>
                                            <a href="{{ route('show', $apartment->id) }}" class="btn btn-primary">Visualizza</a>
                                            <a href="{{ route('edit', $apartment->id) }}" class="btn btn-secondary">Modifica</a>

                                            <form action="{{ route('delete', $apartment->id) }}" method="POST">
                                                @csrf
                                                @method("delete")

                                                <button class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo appartamento?');">Elimina Mauro</button> 
                                            </form>

                                            @if ($apartment ->sponsor)
                                                <span class="btn btn-warning">Sponsorizzato</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>



            <div class="card mt-5">
                <h5 class="card-header"> <b>Privato</b> </h5>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="row">
                        @foreach ($apartments as $apartment)
                            @if ($apartment->user_id === Auth::id() && $apartment->visible == 0 )
                                <div class="col-sm-4 my-3">
                                    <div class="card shadow @if ($apartment->sponsor) bg-info  @endif">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $apartment->name }}</h5>
                                            <p class="card-text">{{ $apartment->address }}</p>
                                            <p class="card-text">Metri quadrati: {{ $apartment->mq }}</p>
                                            <a href="{{ route('show', $apartment->id) }}" class="btn btn-primary">Visualizza</a>
                                            <a href="{{ route('edit', $apartment->id) }}" class="btn btn-secondary">Modifica</a>

                                            <form action="{{ route('delete', $apartment->id) }}" method="POST">
                                                @csrf
                                                @method("delete")

                                                <button class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo appartamento?');">Elimina</button> 
                                            </form>

                                            @if ($apartment ->sponsor)
                                                <span class="btn btn-warning">Sponsorizzato</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Create --}}
            <div class="text-center my-3">
                <a href="{{route('create')}}" class="btn btn-success">CREATE</a>
            </div>

        </div>
    </div>
</div>
@endsection
