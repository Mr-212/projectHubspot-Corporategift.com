<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\GiftProduct;
use App\Services\Hubspot\HubspotConnector;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\CorporateGiftApiHandle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class HupSpotServiceController extends Controller
{

    private $h_client_id; 
    private $h_client_secret; 
    private $h_redirect_uri; 
    private $h_version;
    private $corporateGiftHandler;
    private $hubspotConnector;
    public function __construct()
    {
        $this->h_client_id= Config::get('constants.hubspot.client_id');
        $this->h_client_secret= Config::get('constants.hubspot.client_secret');
//        $this->h_redirect_uri= Config::get('constants.hubspot.redirect_uri');
        $this->h_redirect_uri= 'https://corporategift.dev-techloyce.com/hupspot-authentication';
        $this->h_version= Config::get('constants.hubspot.version');
        $this->hubspot_url = 'https://api.hubapi.com';

        $this->hubspotConnector =  new HubspotConnector($this->h_client_id,$this->h_client_secret,$this->hubspot_url, $this->h_redirect_uri,$this->h_version);
        $this->corporateGiftHandler = new CorporateGiftApiHandle(Config::get('constants.cg_settings.token'),Config::get('constants.cg_settings.domain_uri'));
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function hupspot_auth_token_generator(Request $request)
    {
        $data_array=array();

        try {
            $token = $this->hubspotConnector->authorize($request->code);
            Log::info('token: '.@json_encode($token));
            //var_dump($token);
            $token_info_arr=array();
            if (isset($token['refresh_token'])) {
                $token_info_arr['refresh_token'] = $token['refresh_token'];
                $token_info_arr['access_token']  = $token['access_token'];
                $token_info_arr['expires_in']     =   $token['refresh_token'];
                $token_info_arr['token_current_date_time'] = Carbon::now()->format('Y-m-d H:i:s');
                file_put_contents(app_path().'/hupspot-token.txt',json_encode($token_info_arr));
                $data_array['status']=true;
                $data_array['access_token']= $token['access_token'];

                $res = $this->hubspotConnector->getOauthInfo($token['access_token']);
                if(isset($res['token']) && !empty($res['token'])){
                    $app['hub_refresh_token'] =    @$token_info_arr['refresh_token'];
                    $app['hub_access_token']  = @$token_info_arr['access_token'] ;
                    $app['hub_expires_in']    =   @$token_info_arr['expires_in'] ;
                    $app['hub_app_id']    =   $res['app_id'];
                    $app['hub_id']    =   $res['hub_id'];
                    $app['hub_user']    =   $res['user'];
                    $app['hub_user_id']    =   $res['user_id'];
                    $app['corporate_gift_token']    =   Config::get('constants.cg_settings.token');
                    // $token_info_arr['token_current_date_time']=Carbon::now()->format('Y-m-d H:i:s');
                    @App::create($app);
                }
            }
            $data_array['status']  = @$token['status'];
            $data_array['message'] = @$token['message'];
            //$gettoken = $this->get_access_token();

//            var_dump($res);

        }
        catch(Exception $e) {

            $data_array['status']=false;
            $data_array['message']='Catch Error, API Request Failed';
            echo 'Message: ' .$e->getMessage();
        }

        return $data_array;

    }


    /**
     * Generate access token
     * Check expiry date
     * @return \Illuminate\Http\Response
     */

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





   /*-------------------------------------------------------------------
     * Hubspot fetch request set
     * Whenever any contacts or leads will view crm will send request
     * @return \Illuminate\Http\Response
     ------------------------------------------------------------------------*/

    public function hupspot_data_fetch_request(Request $request){

        Log::info(@$request->all());
//        Log::info(@$request->getMethod());

        $email =  @$request->get('email');
            //$name  =  @$request->get('firstname'). ' '.@$request->get('lastname');
        //$CorporateGiftGet = $this->getGiftProducts();
        $index = 0;
        $gift_arr=array();
        $gift_arr['results'] = null;

//        if(!empty($CorporateGiftGet)){
//            foreach($CorporateGiftGet as $key_index => $single_CorporateGiftGet_data){
//                $product_gift_id=$single_CorporateGiftGet_data['id'];
//                $gift_arr['results'][$key_index]['objectId']=$product_gift_id;
//                $gift_arr['results'][$key_index]['title']=$single_CorporateGiftGet_data['name'];
//                //$gift_arr['results'][$key_index]['title']='Product gift '. $key_index;
//
//
//                $properties_counter=0;
//                $action_counter=0;
//                //Properties arr
//                if(!empty($single_CorporateGiftGet_data['description'])){
//
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label']='Description';
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType']='STRING';
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value']=strip_tags($single_CorporateGiftGet_data['description']);
//
//                }
//
//                if(!empty($single_CorporateGiftGet_data['price'])){
//
//                    $properties_counter++;
//
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label']='Price';
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType']='CURRENCY';
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value']=$single_CorporateGiftGet_data['price'];
//                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['currencyCode']='USD';
//
//                }
//                //Action arr
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['type']="IFRAME";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['width']="890";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['height']="748";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['uri'] = url('/')."/get_hupspot_send_gift_request?product_id={$product_gift_id}&email={$email}";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['label']="Send Gift";
//            }
//        }





        //Setting save
        //$gift_arr['results'] = null;
//        $gift_arr['settingsAction']['type']='IFRAME';
//        $gift_arr['settingsAction']['width']=890;
//        $gift_arr['settingsAction']['height']=748;
//        $gift_arr['settingsAction']['uri']='https://example.com/settings-iframe-contents';
//        $gift_arr['settingsAction']['label']='Settings';


        //Primaryaction create gift
        $gift_arr['primaryAction']['type']='IFRAME';
        $gift_arr['primaryAction']['width']=1100;
        $gift_arr['primaryAction']['height']=748;
        $gift_arr['primaryAction']['uri']=url('/')."/get_all_gift_products?&email={$email}";;
        $gift_arr['primaryAction']['label']='View Gift Products';


        //$gift_arr['allItemsLink']='Create Gift';
        //$gift_arr['totalCount']=isset($CorporateGiftGet['data'])?count($CorporateGiftGet['data']):0;


        // echo '<pre>';
        // print_r($CorporateGiftGet);

        //Log::channel('HubSpotCrmCardLog')->info($request->all());

      // dd($gift_arr['results']);
        return  json_encode($gift_arr);
    }


    public function getGiftProducts(){

        $CorporateGiftGet = @GiftProduct::pluck('data')->toArray();
        if(empty($CorporateGiftGet)) {
            $CorporateGiftGet =  $this->corporateGiftHandler->getGiftProducts();
            if(isset($CorporateGiftGet['status']) && $CorporateGiftGet['status']){
                foreach ($CorporateGiftGet['data'] as $data){;
                    GiftProduct::create(['product_id'=> $data['id'],'data'=>$data]);
                }
            }
            $CorporateGiftGet = GiftProduct::pluck('data')->toArray();
        }

        return $CorporateGiftGet;
    }


    public function getGiftById(){
        $this->corporateGiftHandler->getGiftById(15979);
    }


    public function createGiftProductOrder(){
        $data = [
            "product_id"=> 16723,
            "customer_id" => 1,
            "gift_message"=>"Dear <First Name> <Last Name>\n\n",
            "email_subject"=>"Hic Global Solution - Sent You a Gift!",
            "can_create_dedicated_links"=> false,
            "can_upgrade_regift"=> false,
            "video_url" => "none",
            "sender_name" => "Wojciech Kaminski",
            "recipients" => [
                    "firstname" => "Jon",
                    "email" => "jon@john.con"

            ],
        ];
        $this->corporateGiftHandler->createGiftProductOrder($data);
    }

    public function createGiftByProductId(){
        $product_id =11623;

        $data = [
            "product_id" => "$product_id",
            "gift_message"=>"Dear <First Name> <Last Name>\n\n",
            "email_subject"=>"Hic Global Solution - Sent You a Gift!",
            "can_create_dedicated_links"=> false,
            "can_upgrade_regift"=> false,
            "video_url" => "none",
            "sender_name" => "Wojciech Kaminski",
            "recipients" => [
                    "firstname" => "Jon",
                    "email" => "jon@john.con"
            ],
        ];
        $data = json_encode($data,1);
        $this->corporateGiftHandler->createGift($data);
    }




    public function getAllGiftProducts(){



        $CorporateGiftGet = $this->getGiftProducts();
        $gift_arr=array();

        if(!empty($CorporateGiftGet)){
            foreach($CorporateGiftGet as $key_index => $single_CorporateGiftGet_data){
                $product_gift_id=$single_CorporateGiftGet_data['id'];
                $gift_arr['results'][$key_index]['objectId']=$product_gift_id;
                $gift_arr['results'][$key_index]['title']=$single_CorporateGiftGet_data['name'];
                //$gift_arr['results'][$key_index]['title']='Product gift '. $key_index;


                $properties_counter=0;
                $action_counter=0;
                //Properties arr
                if(!empty($single_CorporateGiftGet_data['description'])){

                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label']='Description';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType']='STRING';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value']=strip_tags($single_CorporateGiftGet_data['description']);

                }

                if(!empty($single_CorporateGiftGet_data['price'])){

                    $properties_counter++;

                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label']='Price';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType']='CURRENCY';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value']=$single_CorporateGiftGet_data['price'];
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['currencyCode']='USD';

                }
                //Action arr
                $gift_arr['results'][$key_index]['actions'][$action_counter]['type']="IFRAME";
                $gift_arr['results'][$key_index]['actions'][$action_counter]['width']="890";
                $gift_arr['results'][$key_index]['actions'][$action_counter]['height']="748";
                $gift_arr['results'][$key_index]['actions'][$action_counter]['uri'] = url('/')."/get_hupspot_send_gift_request?product_id={$product_gift_id}&email={$email}";
                $gift_arr['results'][$key_index]['actions'][$action_counter]['label']="Send Gift";
            }
        }



        //Setting save
        //$gift_arr['results'] = null;
//        $gift_arr['settingsAction']['type']='IFRAME';
//        $gift_arr['settingsAction']['width']=890;
//        $gift_arr['settingsAction']['height']=748;
//        $gift_arr['settingsAction']['uri']='https://example.com/settings-iframe-contents';
//        $gift_arr['settingsAction']['label']='Settings';


        //Primaryaction create gift
//        $gift_arr['primaryAction']['type']='IFRAME';
//        $gift_arr['primaryAction']['width']=890;
//        $gift_arr['primaryAction']['height']=748;
//        $gift_arr['primaryAction']['uri']=url('/').'/create_gift_form';
//        $gift_arr['primaryAction']['label']='View All';
    }

       /*-------------------------------------------------------------------
     * Hubspot send gift ifram popup
     * Show popup when click send gift action button
     * @return \Illuminate\Http\Response
     ------------------------------------------------------------------------*/

     public function get_hupspot_send_gift_request(Request $request){
        Log::channel('HubSpotCrmCardLog')->info('IFRAM REQUEST');
        Log::channel('HubSpotCrmCardLog')->info($request->all());
        $name = @$request->get('name');
        $email= @$request->get('email');

        $action = view('hubspot.hubspot-sendgift',compact('name','email'))->render();

        return  $action;

     }


     public function get_all_gift_products(Request $request){
         $email = @$request->get('email');
         $gift_products = $this->getGiftProducts();
         $action = view('hubspot.gift_cards',compact('gift_products','email'))->render();
         return  $action;


     }

     public function post_hubspot_send_gift_request(Request $request){
        Log::channel('HubSpotCrmCardLog')->info('IFRAM REQUEST BULK');
        Log::channel('HubSpotCrmCardLog')->info($request->all());

       // $action = view('hubspot.hubspot-sendgift')->render();

     }

     public function callback(){


         return view('auth.corporate_gift_cred');
         //return $this->get_access_token();
     }

     public function create_gift_form(){
         $action = view('hubspot.hubspot-sendgift')->render();
         return  $action;
     }

}
