<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BoolBNB</title>

    <link rel="icon" href="{{ asset('storage/defaultImage/logoboolbnb.jpeg') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
    {{-- Link font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Usando Vite -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @vite('resources/scss/pages/show.scss')
    @vite('resources/scss/pages/footer.scss')

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm bg-dark-gray">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="http://localhost:5174/">
                    <div class="logo">
                        <img src="{{ asset('img/logoBoolbnb.png') }}" alt="">
                    </div>
                    {{-- config('app.name', 'Laravel') --}}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        {{-- Dashboard --}}
                        @auth
                            <li class="nav-item">
                                <a class="nav-link white-pearl" href="{{ url('/dashboard') }}"><span>{{ __('Dashboard') }}</span></a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item white-pearl">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item white-pearl">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }} </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle white-pearl" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('dashboard') }}">{{ __('Dashboard') }}</a>
                                    <a class="dropdown-item" href="{{ url('profile') }}">{{ __('Profile') }}</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
        <footer>
            @include('layouts.footer')
        </footer>
    </div>
</body>

<style lang="scss" scoped>

    
   
    .logo {
        max-width: 180px;
    }

    img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 18px;
    }

    /* Background Colors */
    .bg-white-pearl {
        background-color: #EEEEEE
    }

    .bg-yellow {
        background-color: #FFD369;
    }

    .bg-dark-gray {
        background-color: #393E46;
    }

    .bg-black {
        background-color: #222831;
    }

    /* Colors */
    .white-pearl {
        color: #EEEEEE
    }

    .yellow {
        color: #FFD369;
    }

    .dark-gray {
        color: #393E46;
    }

    .black {
        color: #222831;
    }


</style>

</html>
