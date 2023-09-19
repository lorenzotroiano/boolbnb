@extends('layouts.app')

@section('content')
    <div id="dashboard-container">

        <div id="nav-intro" class="container px-0">

            <div class="row pb-3">
                <h4 class="col-12 fw-bold">
                    Benvenuto {{ Auth::user()->name }}!
                </h4>
                <p class="col-12 fs-6">
                    Benvenuto nella tua area personale! Qui potrai gestire facilmente i tuoi appartamenti con pieno
                    controllo.
                </p>
            </div>

            <nav id="navbarDashboard" class="mt-4">
                <div class="container p-0">
                    <ul class="row m-0 p-0 text-center">

                        {{-- appartamenti --}}
                        <li class="col-4 p-0 link" data-target="appartamenti" id="link-apart">
                            {{ __('Appartamenti') }}
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        {{-- Appartamenti --}}
        <div class="container dashboard pt-4 pb-5 px-0">
            <div class="table-responsive">
                <table class="table table-hover-dash table-borderless align-middle">
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

                                                <button class="btn-danger"
                                                    onclick="return confirm('Sei sicuro di voler eliminare questo appartamento?');">Elimina</button>
                                            </form>
                                            <a href="{{ route('show', $apartment->id) }}"
                                                class="text-decoration-none">Visualizza messaggi</a>
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
                <table class="table table-hover-dash table-borderless align-middle">
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
                                    <td class="my-1 azioni">
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('edit', $apartment->id) }}"
                                                class="text-decoration-none">Modifica</a>
                                            <a href="{{ route('sponsor-form', $apartment->id) }}"
                                                class="text-decoration-none">Sponsorizza</a>
                                            <form action="{{ route('delete', $apartment->id) }}" method="POST">
                                                @csrf
                                                @method('delete')

                                                <button type="submit" class="text-danger border-0 border bg-transparent"
                                                    title="Elimina Appartamento"
                                                    onclick="return confirm('Sicuro di volere eliminare questo elemento?')">
                                                    Elimina appartamento</i>
                                                </button>
                                            </form>
                                            <a href="{{ route('show', $apartment->id) }}"
                                                class="text-decoration-none">Visualizza messaggi</a>
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
        <div class="text-center my-3">
            <a href="{{ route('create') }}" class="text-decoration-none create-button">Crea un nuovo appartamento</a>
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

        .container ul.row {
            list-style-type: none;
        }

        li {
            color: rgb(0, 0, 0);
            font-size: 1.2rem;
        }

        li.active {
            border-bottom: 4px solid #7c8cc6;
            padding-bottom: 0.5rem;
            color: #000000;
            font-size: 1.4rem;
        }

        .container.dashboard-table {
            display: none;
        }

        .table-responsive .table {

            --bs-table-bg: transparent;
        }

        .table-responsive tbody {
            background-color: #ffffff;
        }

        .table-hover-dash tbody tr:hover {
            background-color: #7c8bc61c;
        }

        title - image a.apartment {
            text-decoration: none;
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
            width: 20%;
        }


        #my-modal .modal {

            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
        }

        .modal-buttons {
            text-align: right;
            margin-top: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .create-button {
            font-size: 1.2rem;
            color: #648be9;
        }

        .btn-danger {
            border: 3px solid #ffffff;
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-danger :hover {
            border: 3px solid #ffffff;
            color: #ffffff;
            background-color: #f54052ee;
        }

        .btn-danger :active {
            border: 3px solid #ffffff !important;
            color: #ffff !important;
            background-color: #f54052ee;

        }


        .btn-secondary {
            background-color: #C6AB7C;
            color: white;
            border: 3px solid #ffffff;
        }

        .btn-secondary:hover {
            border: 3px solid #7c7dc6;
            color: #7c7dc6;
            background-color: #ffffff;
        }

        .btn-secondary :active {
            color: #7d7cc6 !important;
        }
    </style>
@endsection
