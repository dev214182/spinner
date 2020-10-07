<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (Auth::check()) 
        <meta name="auth_user" content="{{ $usersAuth }}" /> 
    @endif
    
    <title>Gallega Spinner - Alpha</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
   

    <link rel="icon" href="{{asset('images/spinner-fav.png')}}" sizes="32x32">
    <!-- Styles -->
  
    <link href="{{ asset('css/jquery-ui-1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        var tempHotspots = [];
    </script>
</head>

<body>
    <div id="app">
        <v-app>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <builder-navigation></builder-navigation>
            @yield('content')
        </v-app>
    </div>
    <!-- Scripts -->
  
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui-1.12.1.min.js') }}"></script>
    <script src="{{ asset('js/libpannellum.js') }}"></script>
    <script src="{{ asset('js/pannellum.js') }}"></script>
    <script src="{{ asset('js/sp.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>