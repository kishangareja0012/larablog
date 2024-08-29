<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.css')}}" rel="stylesheet" />
</head>

<body>


    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth
                        <li class="nav-item">
                            <a href="{{ url('post/create') }}" class="nav-link">Add Post</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('post') }}" class="nav-link">My Post</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('like-post') }}" class="nav-link">Liked Post</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('deleted/post') }}" class="nav-link">Deleted Post</a>
                        </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
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
                        <li class="nav-item">
                            <a href="{{ url('chat') }}" class="nav-link">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('profile/'.auth()->id()) }}" class="nav-link font-weight-bolder">{{ ucfirst(auth()->user()->name) }}</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link" href="{{ route('register') }}">Logout</button>
                            </form>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="toast d-none" data-delay="3000" style="position: fixed; top: 20px; right: 20px;max-width:300px;width:100%;z-index:1000">
                <div class="toast-header">
                    <strong class="mr-auto">Like</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body pb-0" id="toast-body">
                </div>
            </div>
            @yield('content')
        </main>
    </div>

    <script src="https://kit.fontawesome.com/507bb7fd3a.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}" crossorigin="anonymous"></script>
    <script>
        Echo.private(`likePost.{{ auth()->id() }}`).listen('LikePost', (e) => {
            if (e.type == "like") {
                url = `{{ url('post/view/${e.from_id}') }}`
            } else if (e.type == "message") {
                url = `{{ url('message/${e.from_id}') }}`
            }

            $(".toast").toast('show')
            $("#toast-body").append(`
                    <a href="${url}" class="d-block text-black  pb-3">${e.message}</a>
                `)
        })
    </script>
    @yield('scripts')
</body>

</html>