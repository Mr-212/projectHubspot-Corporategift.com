<html>
    <head> 
        @include('layouts.headers')
    </head>
    <body>
        <div class="container">
            @include('layouts.nav-bar')
            @yield('content')
        </div>
    </body>

<footer>
    @include('layouts.footers')
</footer>
</html>