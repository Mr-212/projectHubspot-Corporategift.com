<html>
    <head> 
        @include('layouts.headers')
    </head>
    <body>
        @include('layouts.nav-bar')
        <div class="container">
            @if ( Session::has('flash_message') )
                <div class="alert {{ Session::get('flash_type') }} alert-dismissible fade show mt-5" role="alert">
                    <strong>{{ Session::get('flash_message') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                </div>
  
            @endif
            @yield('content')
        </div>
    </body>

<footer class="">
  
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
        <div class="fixed-bottom  copyright bg-light shadow sm:rounded-lg">
            <div class="">
                <div class="row">
                    <div class="col-sm-12 copyright-center text-center py-3">
                        © 2020 CorporateGift.com. All Rights Reserved.                <a rel="_blank" href="http://www.saydigitaldesign.com/">Design by Say Digital Design</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers')
</footer>
</html>