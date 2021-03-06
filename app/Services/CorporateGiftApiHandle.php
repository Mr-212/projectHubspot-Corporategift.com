<?php

namespace App\Services;
use Illuminate\Support\Facades\Config;

class CorporateGiftApiHandle
{

    private $headers = [];
    private $access_token, $domain ;

    public function __construct($access_token = null,$domain = null)
    {
        $this->access_token = $access_token ?: Config::get('constants.cg_settings.token');
        $this->domain = $domain ?: Config::get('constants.cg_settings.domain_uri');
        $this->setHeaders();
    }

    private function setHeaders(){
     $this->headers = array(
         "Authorization: Bearer {$this->access_token}",
         'Accept: application/json',
     );
    }

    public function setAccessToken($acces_token){
          $this->access_token = $acces_token;
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
        $res = curl_exec($ch);
         $_res = json_decode($res,1);

        curl_close($ch);
        return $_res;
        // return $res;
    }


    /*----------------------------------------------------------
    **Corporate Gift card get request handlere
    ---------------------------------------------------------*/
    public function getGiftProducts(){
        $url = $this->domain.'/gift/products';
        $response_arr=array();
        $response = $this->curl_request($url,null,'GET',$this->headers);

        if(!empty($response) && !empty($response['data'])){
            $response_arr['status']=true;
            $response_arr['data']=$response['data'];
            $response_arr['message']='Api Ran Successfully!';

        }
        else{
            $response_arr['status']=false;
            $response_arr['data'] = @$response['message'];
            $response_arr['message']='Api Ran Successfully!';
        }
        return $response_arr;
    }


    public function getGiftById($id){
        $url = $this->domain."/gift/$id";
        return  $this->curl_request($url,null,'GET',$this->headers);
    }

    public function createGiftProductOrder($data){
        $url = $this->domain."/ecp_egift/api/create";
        return $this->curl_request($url,$data,'POST',$this->headers);

    }

    public function createGift($data){
        $url = $this->domain."/gift";
        return $this->curl_request($url,$data,'POST',$this->headers);
    }

}
