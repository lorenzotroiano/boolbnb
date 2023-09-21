@extends('layouts.app')
@section('content')
    <div class="container main">

        <div class="row">
            <div>
                <h1>{{ $apartment->name }}</h1>
                <span class="address d-block text-secondary mb-3"> <i class="fa-solid fa-map"></i> -
                    {{ $apartment->address }} </span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 image-container rounded overflow-hidden">
                @if ($apartment->cover)
                    <img src="{{ asset('storage/' . $apartment->cover) }}" class="img-fluid rounded mb-4"
                        alt="Apartment Image">
                @else
                    <div class="alert alert-warning">Immagine non disponibile</div>
                @endif
                @if (isset($apartment) && isset($apartment->images))
                    <div class="d-flex secondary-images-container rounded-bottom">
                        @foreach ($apartment->images as $image)
                            <img class="secondary-image mx-2" src="{{ asset('storage/' . $image->image) }}" alt="Image">
                        @endforeach
                    </div>
                @endif
                {{-- Tempo mancante sponsorizzazioni --}}
                @if (Auth::check() && $apartment->sponsor === 1)
                    <div class="sponsor-time">
                        @foreach ($apartmentsponsors as $apartmentsponsor)
                            @if ($apartmentsponsor->apartment_id === $apartment->id)
                                @php
                                    // Converto in formato data
                                    $startDate = \Carbon\Carbon::parse($apartmentsponsor->start_date);
                                    $endDate = \Carbon\Carbon::parse($apartmentsponsor->end_date);
                                    $hoursDifference = $endDate->diffInHours($startDate);
                                @endphp
                                <div>
                                    <strong>Tempo rimasto di sponsorizzazione</strong> {{ $hoursDifference }} hours
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                {{-- Numero di visualizzazioni --}}
                @if (Auth::check() && $apartment->user_id === Auth::id())
                    <div class="views-apartment">
                        <span>Il tuo appartamento è stato visualizzato da <strong>{{ $apartment->views->count() }}</strong>
                            utenti nelle
                            ultime
                            24h</span>
                    </div>
                @endif
            </div>

            <div class="col-lg-6 col-md-12">
                <!-- Info Appartamento -->
                <div class="apartment-info mb-3 mb-lg-0">
                    {{-- servizi --}}
                    <div class="row">
                        {{-- servizi --}}
                        <div class="col-md-4">
                            <div class="services">
                                <h3 class="text-start">Servizi</h3>
                                <ul class="text-start">
                                    @foreach ($apartment->services as $service)
                                        <li class="list-service text-start">
                                            <i class="{{ $service->icon }}"></i> {{ $service->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="col-md-3 mt-3">
                            <div class="d-flex flex-column">
                                <!-- Icona della casa -->
                                <div class="d-flex align-items-center mb-2 text-white info-color rounded p-1">
                                    <i class="fas fa-home me-2"></i>
                                    <span>Stanze {{ $apartment->room }}</span>
                                </div>

                                <!-- Icona del gabinetto -->
                                <div class="d-flex align-items-center mb-2 text-white info-color rounded p-1">
                                    <i class="fas fa-toilet me-2"></i>
                                    <span>Bagni {{ $apartment->bathroom }}</span>
                                </div>

                                <!-- Icona dei metri quadrati -->
                                <div class="d-flex align-items-center mb-2 text-white info-color rounded p-1">
                                    <i class="fas fa-ruler-combined me-2"></i>
                                    <span>Mq {{ $apartment->mq }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-4">Info appartamento {{ $apartment->description }}</p>

                <div class="message-box mt-4">
                    <div class="message-header">I Tuoi Messaggi</div>
                    @foreach ($apartment->messages as $message)
                        <div class="message-name">Nome: {{ $message->name }}</div>
                        <div class="message-email">Email: {{ $message->email }}</div>
                        <div class="message-text">Messaggio: {{ $message->body }}</div>
                        <div class="message-divider"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


@endsection
