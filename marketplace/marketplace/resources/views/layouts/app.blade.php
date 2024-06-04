<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Marketplace') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>
<body>

<script>
    //get the authenticated user's ID
    var authUserId = {{ auth()->id() }};
</script>

<!-- audio element for alert sound -->
<audio id="alertSound" src="{{ asset('sounds/alert.mp3') }}" preload="auto"></audio>

<!-- Pusher library -->
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    
<!-- Pusher setup -->
<script src="{{ asset('js/pusher.js') }}"></script>


    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand">{{ config('app.name', 'Marketplace') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto"></ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                        <span class="badge bg-danger ms-2" id="unread-messages-count">{{ $unreadMessagesCount }}</span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                                    <a class="dropdown-item" href="{{ route('messages') }}" id="messages-link">
                                        Messages
                                        @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                            <span class="badge bg-danger ms-2">{{ $unreadMessagesCount }}</span>
                                        @endif
                                    </a>
                                    <a class="dropdown-item" href="{{ route('home') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>

<script>
    //check if you are in /messages
    if (window.location.pathname === '/messages') {
        //find badge
        var messageCountBadge = document.querySelector('#navbarDropdown .badge');
        if (messageCountBadge) {
            messageCountBadge.style.display = 'none';
        }
        //find second badge
        var messagesLinkBadge = document.querySelector('#messages-link .badge');
        if (messagesLinkBadge) {
            messagesLinkBadge.style.display = 'none';
        }
    }
</script>

</body>
</html>
