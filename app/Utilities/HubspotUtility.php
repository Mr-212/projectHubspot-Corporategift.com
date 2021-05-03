<?php
namespace App\Utilities;
use Carbon\Carbon;;
use Session;
use Log;
use App\Models\App;
use App\Services\Hubspot\HubspotConnector; 
use Illuminate\Support\Facades\Config;

class HubspotUtility {

    private $hubspotConnector;

    public function __construct(){
        $this->h_client_id= Config::get('constants.hubspot.client_id');
        $this->h_client_secret= Config::get('constants.hubspot.client_secret');
        $this->h_redirect_uri= Config::get('constants.hubspot.redirect_uri');
        $this->h_version= Config::get('constants.hubspot.version');
        // $this->hubspot_url = 'https://api.hubapi.com';
        $this->hubspot_url = Config::get('constants.hubspot.api_url');
        $this->hubspotConnector = new HubspotConnector($this->h_client_id, $this->h_client_secret, $this->hubspot_url, $this->h_redirect_uri, $this->h_version);
    }

    public function authenticate($code)
    {

        $resp_array = [
            'error'=> true,
            'type'=> null,
            'alert_type' => null,
            'message' => 'App installation failed.',
        ];


        try {
            $token = $this->hubspotConnector->authorize($code);
            $token_info_arr=array();
            $app = null;
            if (isset($token['refresh_token'])) {
                $token_info_arr['refresh_token'] = $token['refresh_token'];
                $token_info_arr['access_token']  = $token['access_token'];
                $token_info_arr['expires_in']     =   Carbon::now()->addSeconds($token['expires_in'])->toDateTimeString();
                $token_info_arr['token_current_date_time'] = Carbon::now()->format('Y-m-d H:i:s');
                $res = $this->hubspotConnector->getOauthInfo($token['access_token']);
                if(isset($res['token']) && !empty($res['token'])){
                    $appData['hub_refresh_token'] = @$token_info_arr['refresh_token'];
                    $appData['hub_access_token']  = @$token_info_arr['access_token'] ;
                    $appData['hub_expires_in']    = @$token_info_arr['expires_in'] ;
                    $appData['hub_app_id']        = $res['app_id'];
                    $appData['hub_id']       =   $res['hub_id'];
                    $appData['hub_user']    =   $res['user'];
                    $appData['hub_user_id']    =   $res['user_id'];
                    // $token_info_arr['token_current_date_time']=Carbon::now()->format('Y-m-d H:i:s');

                    $identifier = \hash('sha256',$appData['hub_id'].$appData['hub_user_id']);
                    $appData['identifier'] = $identifier;
                    $appData['is_active'] = 1;
                    $appData['user_id'] = auth()->id();
                   
                    $app = App::where(['hub_app_id'=>$appData['hub_app_id'] ,'hub_id'=> $appData['hub_id'], 'hub_user_id' => $appData['hub_user_id']])->first();
                    if(empty($app)) {
                        $appData['unique_app_id'] = mt_rand(1000,99999);
                        $app = @App::create($appData);
                        auth()->user()->app_id = $app->id;
                    }
                    else{
                        if(empty($app->unique_app_id))
                            $appData['unique_app_id'] = mt_rand(1000,99999);
                        $app->update($appData);
                    }
                    if($app){
                        auth()->user()->app_id = $app->id;
                        auth()->user()->save();
                    }
                    session()->put('identifier', @$app->identifier);

                }
            }
            $data_array['status']  = @$token['status'];
            $data_array['message'] = @$token['message'];
            if(session()->has('identifier') && $app && !empty($app->identifier)){
                $resp_array['error'] = false;
                $resp_array['message'] = 'App installed successfully.';
                $resp_array['access_token'] = @$token['message'];
                //$identifier =  session('identifier');
                //return redirect('/dashboard');
                // return view('auth.corporate_gift_cred',compact('identifier'));
            }
        }
        catch(Exception $e) {
            $resp_array['error'] = true;
            $resp_array['message'] = $e->getMessage();
            // echo 'Message: ' .$e->getMessage();
        }

        return $resp_array;

    }


    public function refresh_access_token($identifier){
        
        $resp_array = [
                'error'=> true,
                'type'=> null,
                'alert_type' => null,
                'message' => null,
            ];

        try {
            $app = $this->getAppByIdentifier($identifier);
            $mindiff = Carbon::now()->diffInMinutes($app->hub_expires_in,false);
            if($mindiff <= 30){
                $token = $this->hubspotConnector->refresh_access_token($app->hub_refresh_token);
                Log::info($token);
                if (isset($token['refresh_token'])) {
                    $token_info_arr['hub_refresh_token']= $token['refresh_token'];
                    $token_info_arr['hub_access_token'] = $token['access_token'];
                    $token_info_arr['hub_expires_in']   = Carbon::now()->addMinutes($token['expires_in']);
                    if($app->update($token_info_arr)){
                        $resp_array['error'] = false;
                        $resp_array['type'] = 'success';
                        $resp_array['alert_type'] = 'alert-success';
                        $resp_array['message'] = 'Access token refreshed successfully.';
                    }
                }else{
                    auth()->user()->update(['app_id'=>null]);
                    $resp_array['error'] = true;
                    $resp_array['type'] = 'disconnected';
                    $resp_array['alert_type'] = 'alert-warning';
                    $resp_array['message'] = 'App is disconnected or malfunctioned refresh token.';
                }
            }else{
                $resp_array['error'] = false;
                $resp_array['type'] = 'warning';
                $resp_array['alert_type'] = 'alert-warning';
                $resp_array['message'] = 'Access token has '.$mindiff. ' minutes left to refresh';
            }
        }
        catch(Exception $e) {
                        $resp_array['error'] = true;
                        $resp_array['type'] = 'error';
                        $resp_array['alert_type'] = 'alert-danger';
                        $resp_array['message'] = $e->getMessage();
                        Log::error($e->getMessage());
        }  
       return $resp_array;
    }


    public function getAppByIdentifier($identifier){
        return App::where('identifier',"{$identifier}")->first();
    }

 }