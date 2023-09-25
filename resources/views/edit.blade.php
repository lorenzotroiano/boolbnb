@extends('layouts.app')
@section('content')
    <div class="container-fluid" id="forms">
        <h2>Modifica il tuo appartamento</h2>
        <div class="row justify-content-center container" id="content-form">
            <div class="col-12 col-lg-9 col-xl-7">
                <form method="POST" action="{{ route('update', $apartment->id) }}" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')
                    <div class="row">

                        <div class="mb-3 col-12">
                            <label for="name"><strong>Nome appartamento</strong> </label>
                            <input required class="form-control" type="text" id="name" name="name"
                                value="{{ $apartment->name }}">
                            @error('title')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label for="description"><strong>Descrizione</strong> </label>
                            <textarea class="form-control" name="description" id="description" rows="5" value="{{ $apartment->description }}">{{ $apartment->description }}</textarea>
                            @error('description')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label for="room"><strong>Stanze</strong></label>
                            <input required class="form-control" type="number" id="room" name="room" min="0"
                                value="{{ $apartment->room }}">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('rooms')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label for="bathroom"><strong>Bagni</strong></label>
                            <input required class="form-control" type="number" id="bathroom" name="bathroom"
                                min="0" value="{{ $apartment->bathroom }}">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('bathrooms')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label for="mq"><strong>Metri Quadrati</strong></label>
                            <input required class="form-control" type="number" id="mq" name="mq" min="0"
                                value="{{ $apartment->mq }}">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('square_meters')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label for="address"><strong>Indirizzo</strong></label>
                            <input required class="form-control" type="text" id="address" name="address"
                                value="{{ $apartment->address }}">
                            @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback">Inserisci un indirizzo valido!</div>
                        </div>

                        <div class="mb-3 col-12">
                            <label class="d-block" for="visible"><strong>Vuoi rendere visibile il tuo
                                    appartamento?</strong></label>
                            <input type="radio" name="visible" id="visible-yes" value="1"
                                @if ($apartment->visible == 1) checked @endif required>
                            <label for="visible">Si</label><br>
                            <input type="radio" name="visible" id="visible-no" value="0"
                                @if ($apartment->visible == 0) checked @endif required>
                            <label for="visible">No</label><br>
                            @error('visible')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <div class="row">
                                <label for="cover" class="form-label col-12"><strong>Immagine principale
                                        dell'appartamento </strong></label>
                                <div class="col-6">
                                    <input type="file" class="form-control" name="cover" id="cover"
                                        value="{{ $apartment->cover }}">
                                    @error('cover')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    @if ($apartment->cover)
                                        <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px"
                                            alt="Immagine appartamento">
                                    @else
                                        Immagine non disponibile
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 col-12">
                            <label for="url" class="form-label"> <strong>Inserire l'album immagini</strong></label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple>
                        </div>
                    </div>

                    <div class="mb-3 col-12">
                        <label class="form-label" for="service"><strong>Servizi</strong></label>
                        <div id="checkbox-feedback" class="invalid-feedback"></div>
                        <div class="row ps-2">
                            @foreach ($services as $service)
                                <div class="form-check col-12 col-md-6 ps-0">
                                    <div class="d-flex">
                                        <input class="form-check-input" type="checkbox" value="{{ $service->id }}"
                                            name="services[]" id="service-{{ $service->id }}"
                                            {{ $apartment->services->contains($service->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service-{{ $service->id }}">
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Bottone di submit per inviare il form -->
                    <button id="submit" class="btn btn-success my-3 w-25 col-12 m-auto" type="submit">Salva</button>
            </div>
            </form>
        </div>
    </div>
    </div>

    <style scoped>
        #forms {
            background: #648be9;
            background: linear-gradient(140deg, rgb(124, 136, 198) 0%, rgb(142, 155, 206) 2%, rgb(176, 181, 221) 10%, rgb(207, 213, 235) 31%, rgba(255, 255, 255, 1) 100%);
            padding: 4rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;

        }

        #submit {
            background-color: #2249e6;
            color: white;
            border: 3px solid transparent;
        }

        #submit :hover {
            border: 3px solid #ffffff;
            color: #ffffff;
        }

        #submit :active {
            border: 3px solid #ffffff !important;
            color: #ffff !important;
        }

        #content-form {
            padding: 1rem 0;
            background-color: rgba($color: #ffffff, $alpha: 0.2);
            border-radius: 15px;
        }
    </style>
@endsection
