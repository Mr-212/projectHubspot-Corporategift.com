
@extends('layouts.app')
@section('content')

{{-- <div class="sidenav">
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
    <a href="#">Intduction</a>
    <a href="#">Setup</a>
    <a href="#">Login/Register</a>
    <a href="#"><strong>Hubspot</strong> Authentication</a>
  </div>
</div> --}}
  
  <!-- Page content -->
  <div class="main mt-5">
    <div class="introduction">
       <h3>What we offer</h3>
       <p><strong>CorporateGift</strong> let you can sent gift to contacts right through <strong>Hubspot</strong> Contact Page and track status of all the gifts and autoupdate daily gift products.</p>
    </div>

    <div class="">
        <h3>Setup Process</h3>
        <ul>
            <li>
                <p>Click on Login, Where you can login with existing account or sign up with new account.</p>
            </li>
            <li>
                <p>Aftr Login/Signup you will be redirected to dashborad you can see you user details and Connected to <strong>Hubspot</strong> button.</p>
            </li>
            <li>
                <p>A <strong>Hubspot</strong> Authentication process will be intialized after clicking on connect to <strong>Hubspot</strong> button, where <strong>Hubspot</strong> ask for required permission to provide <strong> CorporateGift</strong> access to <strong>Hubspot</strong> Contatcts and Other modules info, 
                    upon granting permission you will be redirected back to <strong>CoporateGift</strong> Dashboard page.</p>
            </li>
            <li>
                <p>Dashborad page will show a unique App Id on successfull <strong>Hubspot</strong> Authentication and acuiring Access Token.
                    You can see an Input field labeling Coporate Gift Token, where you can enter you <strong>CoporateGift</strong> access token provided by <strong> CorporateGift.com</strong> and update.
                </p>
            </li>
            <li>
                <p>After successfullY saving Corporate Gift Token, now you can login to <strong>Hubspot</strong> and open any contact page you will see a crm card with, button view all gifts where you can view all the gifts product avaiable, and can send gift to that contant.</p>
            </li>
        </ul>
     </div>

     <div class="">
        <h3 class="">Uninstallation Process</h3>
        <ul>
            <li>
                <p>You can see <strong>connected apps</strong> in <strong>Hubspot</strong> by goto setting and in left navbar in Itegration section.</p>
            </li>
            <li>
                <p>In <strong>connected apps</strong> find an application to disconnect and uninstall it.</p>
            </li>
            <li>
                <p>Upon uninstalling app from <strong>Hubspot</strong>, the host app can no longer access the <strong>Hubspot</strong> and the refresh token is invalidated.</p>
            </li>
            <li>
                <p>In <strong>Hubspot</strong> when you login OR/ if you are already logged into <strong> CorporateGift</strong>, click on refresh aceess token, then your app will disappear and you will see a notification "App is disconnected or malfunctioned refresh token".
                Now your app is disconnected from <strong>Hubspot</strong>.</p>

            </li>
        </ul>
     </div>

     
  </div>
@stop

@push('scripts')

@endpush


