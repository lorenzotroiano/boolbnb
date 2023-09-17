@extends('layouts.app')

@section('content')
    <div id="forms">
        <div class="container mt-4">
            <div class="row justify-content-center" id="content-form">
                <div class="col-md-8">
                    <div class="">
                        <div class="mb-5 logo">
                            <h4>{{ __('Login') }}</h4>
                            <div class="logo-mobile"><img src="{{ asset('img/logoBoolbnb-mobile.png') }}" alt=""></div>
                        </div>


                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-4 row">
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-right">{{ __('La tua E-Mail') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="password"
                                        class="col-md-4 col-form-label text-md-right">{{ __('La tua Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('Ricordati') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4 row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn me-3">
                                            {{ __('Login') }}
                                        </button>

                                        @if (Route::has('password.request'))
                                            <a class="btn-link" href="{{ route('password.request') }}">
                                                {{ __('Password dimenticata?') }}
                                            </a>
                                        @endif
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
