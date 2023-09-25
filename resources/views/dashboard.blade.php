@extends('layouts.app')

@section('content')
    <div id="dashboard-container">

        <div id="nav-intro" class="container px-0">

            <div class="row pb-3">
                <h4 style="text-transform: capitalize" class="col-12 fw-bold">
                    Benvenuto {{ Auth::user()->name }}
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
                                    </td>
                                    <td class="my-1 azioni">
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('edit', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-edit"></i> Modifica
                                            </a>
                                            <a href="{{ route('sponsor-form', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-star"></i> Sponsorizza
                                            </a>
                                            <a href="{{ route('show', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-info-circle"></i> Dettagli
                                            </a>
                                            <form action="{{ route('delete', $apartment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn delete-btn" title="Elimina Appartamento">
                                                    <i class="fas fa-trash-alt"></i> Elimina
                                                </button>
                                            </form>
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
                                            <a href="{{ route('edit', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-edit"></i> Modifica
                                            </a>
                                            <a href="{{ route('sponsor-form', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-star"></i> Sponsorizza
                                            </a>
                                            <a href="{{ route('show', $apartment->id) }}" class="btn custom-btn">
                                                <i class="fas fa-info-circle"></i> Dettagli
                                            </a>
                                            <form action="{{ route('delete', $apartment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn delete-btn" title="Elimina Appartamento">
                                                    <i class="fas fa-trash-alt"></i> Elimina
                                                </button>
                                            </form>
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
            <a href="{{ route('create') }}" class="text-decoration-none create-apartament">Aggiungi un nuovo appartamento</a>
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

        .elimina {
            font-size: 1rem !important;
            transition: transform 0.5s ease-in-out !important;
            color: rgb(222, 51, 51) !important;

        }

        .elimina:hover {
            transform: scale(1.14) !important;
            color: rgb(187, 0, 0) !important;
        }

        .btn {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .custom-btn {
            background-color: #35393fb0;
            color: #ffffff;
        }

        .custom-btn:hover {
            background-color: #222831;
            color: #ffffff;
        }

        .delete-btn {
            background-color: red;
            color: white;
        }

        .delete-btn:hover {
            background-color: darkred;
            color: #f0f0f0;
        }

        .custom-btn, .delete-btn {
            width: 130px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px; 
            font-size: 0.8rem; 
        }

        #confirmation-div {
            display: none;
            z-index: 1050; /* Porta sopra gli altri elementi */
            width: 300px; /* o quanto preferisci */
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040; /* Porta sopra il contenuto ma sotto il div di conferma */
        }

        .navbar li:hover {
            border-bottom: 2px solid yellow;
            color: initial !important; /* Per mantenere il colore iniziale durante l'hover */
        }

        .navbar li a {
            color: whitesmoke; /* Per fare in modo che il colore del testo sia ereditato dall'elemento li */
            text-decoration: none; /* Per rimuovere l'effetto sottolineato */
        }

        .navbar li a:hover {
            color: whitesmoke; /* Per mantenere il colore iniziale durante l'hover */
        }
    </style>
@endsection

<div id="confirmation-div" class="position-fixed top-50 start-50 translate-middle bg-danger text-white p-4 rounded">
    <p>Sei sicuro di voler eliminare questo elemento?</p>
    <button id="confirmDeleteBtn" class="btn btn-outline-light me-2">Conferma</button>
    <button id="cancelBtn" class="btn btn-light">Annulla</button>
</div>
  

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmationDiv = document.getElementById('confirmation-div');
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
    
        let formToDelete; // Variabile per salvare il form da eliminare
    
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                formToDelete = e.target.closest('form'); // Salva il form associato al pulsante 'Elimina' premuto
                document.body.appendChild(overlay);
                confirmationDiv.style.display = 'block';
            });
        });
    
        document.getElementById('cancelBtn').addEventListener('click', function () {
            confirmationDiv.style.display = 'none';
            document.body.removeChild(overlay);
        });
    
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if(formToDelete) formToDelete.submit(); // Sottometti il form se esiste
            confirmationDiv.style.display = 'none';
            document.body.removeChild(overlay);
        });
    });
</script>
