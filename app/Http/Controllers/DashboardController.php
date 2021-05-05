<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utilities\HubspotUtility;
use App\Models\CorporateGiftApiHandle;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;


class DashboardController extends Controller
{
    
    private $hubspotUtility;
    public function __construct(){
         $this->hubspotUtility = new HubspotUtility();
    }

    public function index(){
        if(Auth::check()){
            $this->check_hub_app_status();
            return redirect('/dashboard');
        }
        else
            return redirect('setup-guide');  
    }


    public function dashboard(){
        return view('hubspot.dashboard');
    }

    private function check_hub_app_status(){
       $res = null;
        if(auth()->user()->app_id){
            $app = auth()->user()->app;
            $res = $this->hubspotUtility->hub_app_status($app);
            if($res['alert_type']){
                session()->flash('flash_type', $res['alert_type']);
                session()->flash('flash_message', $res['message']);
            }
        }
        return $res;
    }
}
