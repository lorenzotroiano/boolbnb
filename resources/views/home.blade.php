@extends('layouts.app')
@section('content')

    {{-- Guest --}}
    @guest
    <div>
        <h1>Home BoolBNB Guest</h1>
        <ul>
            @foreach ($apartments as $apartment)
                @if ($apartment->visible)
                    <li ><a style="color: red" href="{{route('show', $apartment->id)}}">{{$apartment->name}}</a></li>
                @else
                    <li><a href="{{route('show', $apartment->id)}}">{{$apartment->name}}</a></li>
                @endif

            @endforeach
        </ul>
    </div>
    @endguest

    {{-- Auth --}}
    @auth
    <div>
        <h1>Home BoolBNB {{ Auth::user()->name }}</h1>
        <ul>
            @foreach ($apartments as $apartment)
                @if ($apartment->visible)
                    <li ><a style="color: red" href="{{route('show', $apartment->id)}}">{{$apartment->name}}</a></li>
                @else
                    <li><a href="{{route('show', $apartment->id)}}">{{$apartment->name}}</a></li>
                @endif

            @endforeach

        </ul>

        <button><a href="{{route('create')}}">+</a></button>
    </div>
    @endauth
    

@endsection