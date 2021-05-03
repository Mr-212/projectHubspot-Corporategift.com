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
  

        // $this->corporateGiftHandler = new CorporateGiftApiHandle(Config::get('constants.cg_settings.token'),Config::get('constants.cg_settings.domain_uri'));
         $this->hubspotUtility = new HubspotUtility();
    }

    public function index(){
        if(Auth::check())
            return redirect('/dashboard');
        else
            return redirect('auth/login');
          
    }


    public function dashboard(){
        return view('hubspot.dashboard');
    }
}
