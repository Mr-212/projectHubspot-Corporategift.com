<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct(){

    }

    public function index(){
        if(Auth::check())
           return view('hubspot.home');
        else
            return redirect('auth/login');
          
    }


    public function home(){
        return view('hubspot.home');
    }
}
