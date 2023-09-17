@extends('layouts.app')

@section('content')
    <div id="forms">
        <div class="container mt-5 py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="">
                        <div class="mb-5 logo">
                            <h4>{{ __('Registrati') }}</h4>
                            <div class="logo-mobile"><img src="{{ asset('img/logoBoolbnb-mobile.png') }}" alt=""></div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-4 row">
                                    <label for="name"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Nome') }}</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name') }}" required autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-right">{{ __('E-mail') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- BIRTH_DATE --}}
                                <div class="mb-4 row">
                                    <label for="birth_date"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Data di Nascita') }}</label>

                                    <div class="col-md-6">
                                        <input id="birth_date" type="date"
                                            class="form-control @error('birth_date') is-invalid @enderror" name="birth_date"
                                            value="{{ old('birth_date') }}" required autocomplete="birth_date"
                                            max="2007-12-31">

                                        @error('birth_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="mb-4 row">
                                    <label for="password"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="password-confirm"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="mb-4 row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn">
                                            {{ __('Registrati') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        #forms {
            background: #648be9;
            background: linear-gradient(140deg, rgb(124, 136, 198) 0%, rgb(142, 155, 206) 2%, rgb(176, 181, 221) 10%, rgb(207, 213, 235) 31%, rgba(255, 255, 255, 1) 100%);
            padding: 4rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo-mobile {
            max-width: 34px;
            margin: 0 0 0 14px;
        }

        .logo-mobile img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 50%;
        }

        #submit {
            background-color: #7c8bc6;
            color: white;
            border: 3px solid transparent;
        }

        #create :hover {
            border: 3px solid #ffffff;
            color: #ffffff;
        }

        #create :active {
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
