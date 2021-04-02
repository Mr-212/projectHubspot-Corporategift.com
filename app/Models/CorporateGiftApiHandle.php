<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;


class CorporateGiftApiHandle
{
    

  
    /*----------------------------------------------------------
    **Corporate Gift card get request handlere
    ---------------------------------------------------------*/
    public static function corporate_gift_get_request($request_end_point=''){

        $cg_acces_token= Config::get('constants.cg_settings.token');
        $cg_domain_uri= Config::get('constants.cg_settings.domain_uri');



        $response_arr=array();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $cg_domain_uri.$request_end_point,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $cg_acces_token",
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $response = json_decode( $response,true);

        if(!empty($response) && !empty($response['data'])){

            $response_arr['status']=true;
            $response_arr['data']=$response['data'];
            $response_arr['message']='Api Ran Successfully!';

        }
        else{
            $response_arr['status']=false;
            $response_arr['data']=$response['message'];
            $response_arr['message']='Api Ran Successfully!';
        }
   
        
        return $response_arr;

    }

}
