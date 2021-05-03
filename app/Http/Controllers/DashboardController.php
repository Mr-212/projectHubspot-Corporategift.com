<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utilities\HubspotUtility;
use App\Services\Hubspot\HubspotConnector;
use App\Models\CorporateGiftApiHandle;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;


class DashboardController extends Controller
{

    public function __construct(){
  

         $this->hubspotUtility = new HubspotUtility();
    }

    public function index(){
        if(Auth::check()){
            $this->check_hub_app_status();
            return redirect('/dashboard');
        }
        else
            return redirect('auth/login');
          
    }


    public function dashboard(){
        return view('hubspot.dashboard');
    }

    private function check_hub_app_status(){
        if(auth()->user()->app_id){
            $app = auth()->user()->app;
            $this->hubspotUtility->hub_app_status($app);
        }
    }
}
