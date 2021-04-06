<?php

namespace App\Models;
use Illuminate\Support\Facades\Config;

class CorporateGiftApiHandle
{

    private $headers = [];
    private $acces_token, $domain ;

    public function __construct($access_token = null,$domain = null)
    {
        $this->acces_token = $access_token ?: Config::get('constants.cg_settings.token');
        $this->domain = $domain ?: Config::get('constants.cg_settings.domain_uri');
        $this->setHeaders();
    }


    private function setHeaders(){
     $this->headers = array(
         "Authorization: Bearer {$this->acces_token}",
         'Accept: application/json',
     );
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
            $response_arr['data']=@$response['message'];
            $response_arr['message']='Api Ran Successfully!';
        }
        
        return $response_arr;
    }


    public function getGiftById($id){
        $url = $this->domain."/gift/$id";
        $response = $this->curl_request($url,null,'GET',$this->headers);
        dd('here',$response);
    }

    public function createGiftProductOrder($data){
        $url = $this->domain."/ecp_egift/api/create";
        $response = $this->curl_request($url,$data,'POST',$this->headers);
        dd($response);
    }

    public function createGift($data){
        $url = $this->domain."/gift";
//        dd(json_encode($data));
        $response = $this->curl_request($url,$data,'POST',$this->headers);
        dd($response);
    }

}
