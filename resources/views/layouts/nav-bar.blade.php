
@if (Route::has('login'))
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <nav class="navbar fixed-top navbar-expand-lg  bg-light shadow sm:rounded-lg">
        <a class="navbar-brand desktop-navbar-brand" href="https://corporategift.com/" title="CorporateGift.com">
            <img src="https://corporategift.com/skin/frontend/corporategift/default/images/logonew.png" alt="CorporateGift.com" class="logo-image">
        </a>
       
        <ul class="navbar-nav ml-auto">
            @auth
            <a href="{{ url('/Dashboard') }}" class="nav-item nav-link text-sm text-gray-700 underline disabled" >Dashboard</a>
            <li class="nav-item">
                <a href="{{ url('/auth/logout') }}" class="nav-item nav-link text-sm text-gray-700 underline">Logout</a>
            </li>
            @else
            <a href="{{ route('login') }}" class="nav-item nav-link text-sm text-gray-700 underline">Log in</a>
    
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="nav-item nav-link ml-4 text-sm text-gray-700 underline">Register</a>
            @endif
    
            @endauth
           
           
            {{-- <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">            Dropdown on Right</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action with a lot of text inside of an item</a>
              </div>
            </li> --}}
          </ul> 
        </div>
      </nav>
@endif   