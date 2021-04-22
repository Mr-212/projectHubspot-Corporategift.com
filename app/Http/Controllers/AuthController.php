<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index(){
        if(Auth::id()){
            return view('hubspot.home');
        }else{
            return view('auth.sign-up');
        }
         
    }
    

    public function login()
    {
        return view('auth.login');
        // return view('hubspot.home');
    }

    public function sign_up()
    {
        return view('auth.sign-up');
        // return view('hubspot.home');
    }
}
