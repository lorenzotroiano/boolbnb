@extends('layouts.app')
@section('content')
    <div class="container text-center">
        <h1>Create new Apartment</h1>

        <form method="POST" action="{{ route('update', $apartment->id) }}" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @if ($apartment->cover)
                <img src="{{ asset('storage/' . $apartment->cover) }}" width="200px" alt="Immagine di Mauro">
            @else
                Immagine non disponibile
            @endif
            <br>
            <input type="file" name="cover" id="cover" value="{{ $apartment->cover }}">
            <br>
            @error('cover')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <label for="name">name</label>
            <br>
            <input type="text" name="name" id="name" value="{{ $apartment->name }}">
            <br>

            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror



            <label for="description">description</label>
            <br>
            <input type="text" name="description" id="description" value="{{ $apartment->description }}">
            <br>
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="room">room</label>
            <br>
            <input type="number" name="room" id="room" value="{{ $apartment->room }}">
            <br>
            @error('room')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="bathroom">bathroom</label>
            <br>
            <input type="number" name="bathroom" id="bathroom" value="{{ $apartment->bathroom }}">
            <br>
            @error('bathroom')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="mq">mq</label>
            <br>
            <input type="number" name="mq" id="mq" value="{{ $apartment->mq }}">
            <br>
            @error('mq')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="address">address</label>
            <br>
            <input type="text" name="address" id="address" value="{{ $apartment->address }}">
            <br>
            @error('address')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            @foreach ($services as $service)
                <div class="form-check mx-auto" style="max-width: 300px">
                    <input class="form-check-input" type="checkbox" value="{{ $service->id }}" name="services[]"
                        id="service-{{ $service->id }}">
                    <label class="form-check-label" for="service-{{ $service->id }}">
                        {{ $service->name }}
                    </label>
                </div>
            @endforeach

            <!-- Bottone di submit per inviare il form -->
            <input class="my-3" type="submit" value="CREATE">

        </form>
    </div>
@endsection
