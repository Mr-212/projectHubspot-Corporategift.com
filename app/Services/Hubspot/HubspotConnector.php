<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 4/6/21
 * Time: 5:41 PM
 */

namespace App\Services\Hubspot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class HubspotConnector
{

    private $client_id, $client_secret, $redirect_url, $version, $base_url,$access_token,$url;

    public function __construct($client_id, $client_secret, $base_url,$redirect_url, $version)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
        $this->base_url = $base_url;
        $this->version = $version;

    }

    private function curl_request($url, $data = array(), $type = 'GET', $header = array())
    {
        $ch = curl_init($url);

        if ($type == 'POST' || $type == 'PUT') {
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if ($header)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $res = curl_exec($ch);
        $_res = json_decode($res,1);

        curl_close($ch);
        return $_res;
        //return $res;
    }

    public function authorize($code)
    {
        $headers = ['Content-Type: application/x-www-form-urlencoded;charset=utf-8'];
        $params= [
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_url,
        ];
        $url = $this->base_url.'/oauth/'.$this->version .'/token?';
        return $this->curl_request($url,http_build_query($params), 'POST', $headers);
    }


    public function getOauthInfo($access_token){
        $url =  $url = $this->base_url ."/oauth/{$this->version}/access-tokens/{$access_token}";
        return $this->curl_request($url,NULL,'GET',NULL);
    }

    public function get_access_token(){

        $data_array=array();
        try {

            $verifiy_access_token=file_get_contents(app_path().'/hupspot-token.txt');
            $verifiy_access_token=json_decode($verifiy_access_token,true);
            
            $current_date=Carbon::now()->format('Y-m-d H:i:s');
            $token_current_date_time=$verifiy_access_token['token_current_date_time'];
            $mindiff = round((strtotime($current_date) - strtotime($token_current_date_time))/60);

            if($mindiff > 50){

                $params['form_params'] = [
                    'refresh_token' => $verifiy_access_token['refresh_token'],
                    'client_id' =>  $this->h_client_id,
                    'client_secret' => $this->h_client_secret,
                    'grant_type' => 'refresh_token',
                    'redirect_uri' =>   $this->h_redirect_uri,
                ];
                $client = new Client();

                $post_url='https://api.hubapi.com/oauth/'.$this->h_version.'/token';
                //Log::channel('HubSpotCrmCardLog')->info('API LOG');
                //Log::channel('HubSpotCrmCardLog')->info($params);
                $response = $client->post($post_url, $params);

                $token = json_decode($response->getBody());
                $token_info_arr=array();
                if (isset($token->refresh_token)) {

                    $token_info_arr['refresh_token']=$token->refresh_token;
                    $token_info_arr['access_token']=$token->access_token;
                    $token_info_arr['expires_in']=$token->refresh_token;
                    $token_info_arr['token_current_date_time']=Carbon::now()->format('Y-m-d H:i:s');
                    file_put_contents(app_path().'/hupspot-token.txt',json_encode($token_info_arr));
                }

            }


            $verifiy_access_token=file_get_contents(app_path().'/hupspot-token.txt');
            $verifiy_access_token=json_decode($verifiy_access_token,true);
            if(!empty($verifiy_access_token['access_token'])){

                $data_array['status']=true;
                $data_array['access_token']=$verifiy_access_token['access_token'];

            }
            else{
                $data_array['status']=false;
                $data_array['message']='No Token Found!';
            }

        }
        catch(Exception $e) {

            $data_array['status']=false;
            $data_array['message']='Catch Error, No Token Found!';
            //echo 'Message: ' .$e->getMessage();
        }

    
       return $data_array;


    }

}