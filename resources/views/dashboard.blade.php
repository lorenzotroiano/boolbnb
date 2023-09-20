@extends('layouts.app')

@section('content')
    <div id="dashboard-container">

        <div id="nav-intro" class="container px-0">

            <div class="row pb-3">
                <h4 class="col-12 fw-bold">
                    Benvenuto {{ Auth::user()->name }} !
                </h4>
                <p class="col-12 fs-6">
                    Benvenuto nella tua area personale! Qui potrai gestire facilmente i tuoi appartamenti con pieno
                    controllo.
                </p>
            </div>
        </div>
        {{-- Appartamenti --}}
        <div class="container dashboard pt-4 pb-5 px-0">
            <div class="table-responsive">
                <table class="table table-dash table-borderless align-middle">
                    <thead class="table-light">
                        <tr class="border-bottom">
                            <th>Appartamenti pubblici</th>
                            <th class="azioni">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">

                        @php
                            // Ordina gli appartamenti in base al campo sponsor (true prima di false)
                            $sorted_apartments = $apartments->sortByDesc('sponsor');
                        @endphp
                        @foreach ($sorted_apartments as $apartment)
                            @if ($apartment->user_id === Auth::id() && $apartment->visible == 1)
                                <tr>
                                    <td class="my-1">
                                        <div class="d-flex">
                                            <div class="img me-3">
                                                @if ($apartment->cover)
                                                    <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px"
                                                        alt="Immagine appartamento">
                                                @else
                                                    Immagine non disponibile
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column title @if ($apartment->sponsor)  @endif">
                                                <span class="pb-2"> {{ $apartment->name }}</span>
                                                <span class="address">{{ $apartment->address }}</span>
                                                <span class="mq">Metri quadrati: {{ $apartment->mq }}</p>
                                                    @if ($apartment->sponsor)
                                                        <span>Sponsorizzato</span>
                                                    @endif
                                            </div>
                                        </div>
                                    <td class="my-1 azioni">
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('edit', $apartment->id) }}"
                                                class="text-decoration-none">Modifica</a>
                                            <a href="{{ route('sponsor-form', $apartment->id) }}"
                                                class="text-decoration-none">Sponsorizza</a>
                                            <form action="{{ route('delete', $apartment->id) }}" method="POST">
                                                @csrf
                                                @method('delete')

                                                <button type="submit"
                                                    class="text-danger border-0 border bg-transparent elimina"
                                                    title="Elimina Appartamento"
                                                    onclick="return confirm('Sicuro di volere eliminare questo elemento?')">
                                                    Elimina appartamento</i>
                                                </button>
                                            </form>
                                            <a href="{{ route('show', $apartment->id) }}"
                                                class="text-decoration-none">Dettagli e messaggi</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Appartamenti privati --}}
        <div class="container dashboard pt-4 pb-5 px-0">
            <div class="table-responsive">
                <table class="table table-dash table-borderless align-middle">
                    <thead class="table-light">
                        <tr class="border-bottom">
                            <th>Appartamenti privati</th>
                            <th class="azioni">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">

                        @php
                            // Ordina gli appartamenti in base al campo sponsor (true prima di false)
                            $sorted_apartments = $apartments->sortByDesc('sponsor');
                        @endphp
                        @foreach ($sorted_apartments as $apartment)
                            @if ($apartment->user_id === Auth::id() && $apartment->visible == 0)
                                <tr>
                                    <td class="my-1">
                                        <div class="d-flex">
                                            <div class="img me-3">
                                                @if ($apartment->cover)
                                                    <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px"
                                                        alt="Immagine appartamento">
                                                @else
                                                    Immagine non disponibile
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column title @if ($apartment->sponsor)  @endif">
                                                <span class="pb-2"> {{ $apartment->name }}</span>
                                                <span class="address">{{ $apartment->address }}</span>
                                                <span class="mq">Metri quadrati: {{ $apartment->mq }}</p>
                                                    @if ($apartment->sponsor)
                                                        <span>Sponsorizzato</span>
                                                    @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="my-1 azioni">
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('edit', $apartment->id) }}"
                                                class="text-decoration-none">Modifica</a>
                                            <a href="{{ route('sponsor-form', $apartment->id) }}"
                                                class="text-decoration-none">Sponsorizza</a>
                                            <form action="{{ route('delete', $apartment->id) }}" method="POST">
                                                @csrf
                                                @method('delete')

                                                <button type="submit"
                                                    class="text-danger border-0 border bg-transparent elimina"
                                                    title="Elimina Appartamento"
                                                    onclick="return confirm('Sicuro di volere eliminare questo elemento?')">
                                                    Elimina
                                                </button>
                                            </form>
                                            <a href="{{ route('show', $apartment->id) }}"
                                                class="text-decoration-none">Dettagli e messaggi</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CREATE --}}
        <div class="text-center py-3">
            <a href="{{ route('create') }}" class="text-decoration-none create-apartament">Crea un nuovo appartamento</a>
        </div>
    </div>
    <style>
        #dashboard-container {
            background: #648be9;
            background: linear-gradient(140deg, rgb(124, 136, 198) 0%, rgb(142, 155, 206) 2%, rgb(176, 181, 221) 10%, rgb(207, 213, 235) 31%, rgba(255, 255, 255, 1) 100%);
        }

        #nav-intro {
            padding-top: 40px;
        }

        .table-responsive tbody {
            background-color: #ffffff;
        }

        .img img {
            width: 120px;
            height: 80px;
            object-fit: cover;
        }

        .title {
            font-size: 1.2rem;
            color: #000000;
        }

        .address {
            font-size: 0.9rem;
            color: grey;
        }

        .mq {
            font-size: 0.9rem;
            color: grey;
        }

        .table-responsive .azioni {
            width: 42%;
            text-align: center;

        }

        .d-flex a {
            font-size: 1rem;
            transition: transform 0.5s ease-in-out;
            color: #3b70ed;
        }

        .d-flex a:hover {
            transform: scale(1.14);
            color: #0442d2;
        }

        .create-apartament {
            font-size: 1.3rem;
            color: #3b70ed;
        }

        .create-apartament:hover {
            color: #0442d2;
        }

        .elimina {
            font-size: 1rem !important;
            transition: transform 0.5s ease-in-out !important;
            color: rgb(222, 51, 51) !important;

        }

        .elimina:hover {
            transform: scale(1.14) !important;
            color: rgb(187, 0, 0) !important;
        }
    </style>
@endsection
