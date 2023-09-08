@extends('layouts.app')
@section('content')
    <div class="container text-center">
        <h1>Create new Apartment</h1>

        <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data">

            @csrf
            @method('POST')

            <label for="cover">Main picture</label>
            <br>
            <input type="file" name="cover" id="cover">
            <br>
            @error('cover')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <label for="name">name</label>
            <br>
            <input type="text" name="name" id="name" required minlength="3" maxlength="50">
            <br>

            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror



            <label for="description">description</label>
            <br>
            <input type="text" name="description" id="description" required maxlength="255">
            <br>
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="room">room</label>
            <br>
            <input type="number" name="room" id="room" required min="1" max="20">
            <br>
            @error('room')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="bathroom">bathroom</label>
            <br>
            <input type="number" name="bathroom" id="bathroom" required max="4">
            <br>
            @error('bathroom')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="mq">mq</label>
            <br>
            <input type="number" name="mq" id="mq" required min="8">
            <br>
            @error('mq')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror


            <label for="address">address</label>
            <br>
            <input type="text" name="address" id="address" required>
            <br>
            @error('address')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            
            <label for="visible">Visible</label>
            <br>
            <input type="radio" name="visible" id="visible-yes" value="1"> Yes
            <input type="radio" name="visible" id="visible-no" value="0"> No
            <br>
            @error('visible')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
            <br>




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
