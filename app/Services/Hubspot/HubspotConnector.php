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

}