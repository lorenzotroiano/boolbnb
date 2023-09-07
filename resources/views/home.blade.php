@extends('layouts.app')
@section('content')
    <div>
        @guest
            <h1>Home BoolBNB Guest</h1>
        @endguest

        @auth
            <h1>Home BoolBNB {{ Auth::user()->name }}</h1>
        @endauth
        <ul>
            @foreach ($apartments as $apartment)
                @if ($apartment->visible)
                    <li><a style="color: red" href="{{ route('show', $apartment->id) }}">{{ $apartment->name }}</a></li>
                @else
                    <li><a href="{{ route('show', $apartment->id) }}">{{ $apartment->name }}</a></li>
                @endif
            @endforeach
        </ul>
    </div>
@endsection
