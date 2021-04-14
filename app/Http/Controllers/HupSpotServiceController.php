<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\GiftOrder;
use App\Models\GiftProduct;
use App\Services\Hubspot\HubspotConnector;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\CorporateGiftApiHandle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery\Exception;

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

        //ini_set('session.cookie_samesite', 'None');
        //ini_set('session.cookie_secure', 'true');
        $this->h_client_id= Config::get('constants.hubspot.client_id');
        $this->h_client_secret= Config::get('constants.hubspot.client_secret');
//        $this->h_redirect_uri= Config::get('constants.hubspot.redirect_uri');
        $this->h_redirect_uri= 'https://corporategift.dev-techloyce.com/hupspot-authentication';
//        $this->h_redirect_uri= 'https://tame-bobcat-18.loca.lt'.   '/hupspot-authentication';
        $this->h_version= Config::get('constants.hubspot.version');
        $this->hubspot_url = 'https://api.hubapi.com';

//        $this->corporateGiftHandler = new CorporateGiftApiHandle(Config::get('constants.cg_settings.token'),Config::get('constants.cg_settings.domain_uri'));
        $this->hubspotConnector = new HubspotConnector($this->h_client_id, $this->h_client_secret, $this->hubspot_url, $this->h_redirect_uri, $this->h_version);

    }

    public function getAppByHubIdUserId($hub_id, $userId){

            $identifier = null;
            $app = null;
            if(!empty($hub_id) && !empty($userId)) {
                $app = App::where(['hub_id' => $hub_id, 'hub_user_id' => $userId])->first();
                if ($app) {
                    $newIdentifier = hash('sha256',$app->identifier.$hub_id.$userId);
                    if($app->update(['identifier' => $newIdentifier]))
                        $identifier = $newIdentifier;
                }
            }

            return $app;
    }


    public function getAppByIdentifier($identifier){

       return App::where('identifier',"{$identifier}")->first();
    }

    public function getCorporateGiftConnector($corporateGiftToken){

       if(!empty($corporateGiftToken)){
            $this->corporateGiftHandler = new CorporateGiftApiHandle($corporateGiftToken,Config::get('constants.cg_settings.domain_uri'));
        }else{
            return response()->json(['message' =>'Session expired please refresh the page']);
        }

    }
//    public function corporateGiftMerchantHandler(){
//          if(session()->has('corporate_gift_token')){
//              $this->corporateGiftHandler = new CorporateGiftApiHandle(session('corporate_gift_token'),Config::get('constants.cg_settings.domain_uri'));
//
//          }else{
//              return response()->json(['message' =>'Session expired please refresh the page']);
//          }
//    }

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
            $app = null;
            if (isset($token['refresh_token'])) {
                $token_info_arr['refresh_token'] = $token['refresh_token'];
                $token_info_arr['access_token']  = $token['access_token'];
                $token_info_arr['expires_in']     =   $token['refresh_token'];
                $token_info_arr['token_current_date_time'] = Carbon::now()->format('Y-m-d H:i:s');
                //file_put_contents(app_path().'/hupspot-token.txt',json_encode($token_info_arr));


                $res = $this->hubspotConnector->getOauthInfo($token['access_token']);
                if(isset($res['token']) && !empty($res['token'])){
                    $appData['hub_refresh_token'] =    @$token_info_arr['refresh_token'];
                    $appData['hub_access_token']  = @$token_info_arr['access_token'] ;
                    $appData['hub_expires_in']    =   @$token_info_arr['expires_in'] ;
                    $appData['hub_app_id']    =   $res['app_id'];
                    $appData['hub_id']    =   $res['hub_id'];
                    $appData['hub_user']    =   $res['user'];
                    $appData['hub_user_id']    =   $res['user_id'];
                    // $token_info_arr['token_current_date_time']=Carbon::now()->format('Y-m-d H:i:s');

                    $identifier = \hash('sha256',$appData['hub_id'].$appData['hub_user_id']);
                    $appData['identifier'] = $identifier;
                    $app = App::where(['hub_app_id'=>$appData['hub_app_id'] ,'hub_id'=> $appData['hub_id'], 'hub_user_id' => $appData['hub_user_id']])->first();
                    if(empty($app)) {
                        $app = @App::create($appData);
                    }
                    else{
                        $app->update($appData);
                    }
                    session()->put('identifier', @$app->identifier);

                }
            }
            $data_array['status']  = @$token['status'];
            $data_array['message'] = @$token['message'];
            if(session()->has('identifier') && $app && !empty($app->identifier)){
                $hub_id =  session('identifier');
                return view('auth.corporate_gift_cred',compact('identifier'));
            }
        }
        catch(Exception $e) {

//            $data_array['status']=false;
//            $data_array['message']='Catch Error, API Request Failed';
            echo 'Message: ' .$e->getMessage();
        }

       // return $data_array;

    }

    public function post_corporate_gift_token(Request $request){
        //dd($request->all());
        $res = ['status' => false, 'message' => 'Token not updated.' ];
        if(session()->has('identifier')  && $request->has('corporate_gift_token')){
            try {
                //$hub_id = $request->get('hub_id');
                $identifier = session('identifier');
                $corporate_gift_token = $request->get('corporate_gift_token');
                $appExist = App::where('identifier', $identifier)->first();
                if ($appExist) {
                    $appExist->update(['corporate_gift_token' => $corporate_gift_token]);
                    $res = ['status' => true, 'message' => 'Token updated successfully.'];
                }
            }catch (Exception $e){
                return redirect()->back();
            }
        }

        return response()->json($res);
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


        //Log::info($request->headers);
        Log::info(@$request->all());
        session()->put('object_id',$request->get('associatedObjectId'));
        //cache()->put('object_id',$request->get('associatedObjectId'));

        $identifier = null;
        $app = null;
        if($request->has('userId') && $request->has('portalId')) {
            $app = $this->getAppByHubIdUserId($request->get('portalId'),$request->get('userId'));
           $identifier = $app->identifier;
            session()->put('identifier',$app->identifier);
            //cache()->put($identifier,$identifier);
        }


        $name = '';
        $email =  @$request->get('email');
        if($request->has('firstname') && $request->has('lastname'))
            $name  .=  @$request->get('firstname'). ' '.@$request->get('lastname');


        $params['params'] = [
            'identifier'=> @$identifier,
            'name'=>$name,
            'email'=>$email,
            'object_id'=>$request->get('associatedObjectId'),
            'object_type'=>$request->get('associatedObjectType'),
        ];

        cache()->put('app',$params);


        $getGifts = GiftOrder::where('app_id', $app->id)->orderBy('created_at','desc')->limit(10)->get();
        $gift_arr = array();

        if(!empty($getGifts)){
            foreach($getGifts as $key_index => $order){
                $single_CorporateGiftGet_data = @GiftProduct::where('product_id',$order['product_id'])->first()->data;

                $product_gift_id=$single_CorporateGiftGet_data['id'];
                $gift_arr['results'][$key_index]['objectId']=$product_gift_id;
                $gift_arr['results'][$key_index]['title'] = @$single_CorporateGiftGet_data['name'];
                $gift_arr['results'][$key_index]['link'] = "https://development.corporategift.com/media/catalog/product/{$single_CorporateGiftGet_data['image']}";





                $properties_counter=0;
                $action_counter=0;
                //Properties arr
                if(!empty($single_CorporateGiftGet_data['description'])){
//
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label'] = 'Description';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType'] = 'STRING';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value'] = strip_tags(@$single_CorporateGiftGet_data['description']);
                    $properties_counter++;

                }
                if($order['status']) {

                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label'] = 'Status';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType'] = 'STRING';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value'] = $order['status'];
                    $properties_counter++;
                }

                if($single_CorporateGiftGet_data['price']){



                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['label'] = 'Price';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['dataType'] = 'CURRENCY';
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['value'] = @$single_CorporateGiftGet_data['price']?:"--";
                    $gift_arr['results'][$key_index]['properties'][$properties_counter]['currencyCode'] = 'USD';
                    $properties_counter++;

                }
                //Action arr
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['type']="IFRAME";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['width']="890";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['height']="748";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['uri'] = url('/')."/get_hupspot_send_gift_request?product_id={$product_gift_id}&email={$email}";
//                $gift_arr['results'][$key_index]['actions'][$action_counter]['label'] = "View Detail";
            }
        }





        //Setting save
        //$gift_arr['results'] = null;
//        $gift_arr['settingsAction']['type']='IFRAME';
//        $gift_arr['settingsAction']['width']=890;
//        $gift_arr['settingsAction']['height']=748;
//        $gift_arr['settingsAction']['uri']='https://example.com/settings-iframe-contents';
//        $gift_arr['settingsAction']['label']='Settings';

         $url =url('/')."/get_all_gift_products?".http_build_query($params);
        //Primaryaction create gift
        $gift_arr['primaryAction']['type']='IFRAME';
        $gift_arr['primaryAction']['width']=1100;
        $gift_arr['primaryAction']['height']=748;
//        $gift_arr['primaryAction']['uri'] = url('/')."/get_all_gift_products?identifier={$identifier}&email={$email}";
        $gift_arr['primaryAction']['uri'] = $url;
        $gift_arr['primaryAction']['label']='View Gift Products';


        //$gift_arr['allItemsLink']='Create Gift';
        //$gift_arr['totalCount']=isset($CorporateGiftGet['data'])?count($CorporateGiftGet['data']):0;


        // echo '<pre>';
        // print_r($CorporateGiftGet);

        //Log::channel('HubSpotCrmCardLog')->info($request->all());

      // dd($gift_arr['results']);
        return  json_encode($gift_arr);
    }


    public function getGiftProducts($identifier){
        $CorporateGiftGet = null;
        $app = $this->getAppByIdentifier($identifier);
        if($app) {
           $this->getCorporateGiftConnector($app->corporate_gift_token);
            $CorporateGiftGet = @GiftProduct::where('app_id',$app->id)->get()->toArray();
//            $CorporateGiftGet = @GiftProduct::where('app_id',$app->id)->paginate(9);
            if (empty($CorporateGiftGet)) {
                $CorporateGiftGet = $this->corporateGiftHandler->getGiftProducts();
                if (isset($CorporateGiftGet['status']) && $CorporateGiftGet['status']) {
                    foreach ($CorporateGiftGet['data'] as $data) {
                        GiftProduct::updateOrCreate(['app_id'=>$app->id,'product_id'=>$data['id']], ['product_id' => $data['id'], 'data' => $data]);
                    }
                }
                $CorporateGiftGet = GiftProduct::where('app_id',$app->id)->get()->toArray();
//                $CorporateGiftGet = GiftProduct::where('app_id',$app->id)->paginate(9);
            }
        }
//        dd($CorporateGiftGet);

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
        $identifier = '0fe73d585d3a269ac72ea4c88e36eff800d1b56a8e65d29a67d1645d36bd3a80';
        $app = $this->getAppByIdentifier($identifier);
        if ($app)
            $this->getCorporateGiftConnector($app->corporate_gift_token);
        $product_id =11622;

        $data = [
            "product_id" => $product_id,
            "gift_message"=>"Dear <First Name> <Last Name>\n\n",
            "email_subject"=>"Hic Global Solution - Sent You a Gift!",
            "can_create_dedicated_links"=> false,
            "can_upgrade_regift"=> false,
            "video_url" => "none",
            "sender_name" => "Wojciech Kaminski",
            "recipients" => [

                 [
                    "firstname" => "Jon",
                    "email" => "jon@john.con"
                 ]
            ],
        ];
//        $data = "{
//\"product_id\":11623,
//\"gift_message\":\"Dear <First Name> <Last Name>\",
//\"email_subject\":\"Hic Global Solution - Sent You a Gift!\",
//\"can_create_dedicated_links\":false,
//\"can_upgrade_regift\":false,
//\"video_url\":\"none\",
//\"sender_name\":\"Wojciech kaminski\",
//\"recipients\":[{
//\"firstname\":\"jon\",
//\"email\":\"jon@john.con\"
//}]
//}";
        //dd($data);
        $res = $this->corporateGiftHandler->createGift(http_build_query($data));
        dd($res);
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


                $properties_counter = 0;
                $action_counter = 0;
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
        $name = @$request->get('name');
        $email= @$request->get('email');
        $action = view('hubspot.hubspot-sendgift',compact('name','email'))->render();
        return  $action;

     }


     public function get_all_gift_products(Request $request){
         //dd(session('object_id'));
         dd(cache()->get('app'));
         $params = $request->get('params');

         //session()->put('object',123);
         $email = @$request->get('email');
         $name = @$request->get('name');
         $identifier = @$params['identifier'];

//         if(session()->has('identifier') && session('identifier') == $identifier) {
//         $identifier = '0fe73d585d3a269ac72ea4c88e36eff800d1b56a8e65d29a67d1645d36bd3a80';
             $gift_products = $this->getGiftProducts($identifier);
//             $action = view('hubspot.gift_products', compact('gift_products', 'email', 'identifier','name'))->render();
             $action = view('hubspot.gift_products', compact('gift_products', 'params'))->render();
             return  $action;

//         }else{
//             return response()->json('Not Verified');
//         }


     }

     public function post_hubspot_send_gift_request(Request $request){
         //dd(session('object'));
         $form = $request->all();
         dd($request->all());
         $return = ['status'=>false,'data'=>($request->all())];

         $identifier = @$request['identifier'];
         $subject = @$form['subject'];
         $email =  @$form['email'];
         $name = @$form['name'];

         $product_id = $form['product_id'];

         if(!empty($identifier) && $subject && $email) {
//         if(!empty($identifier) && $request->has('product_id') && $request->has('email') && $request->has('subject') && $request->has('message')) {
             $app = $this->getAppByIdentifier($identifier);
             if ($app) {
                 $this->getCorporateGiftConnector($app->corporate_gift_token);


             $data = [
                 "product_id" => $product_id,
                 "gift_message" => "Dear {$name}",
                 "email_subject" => "{$subject}",
                 "can_create_dedicated_links" => false,
                 "can_upgrade_regift" => false,
                 "video_url" => "none",
                 "sender_name" => "Wojciech Kaminski",
                 "recipients" => [
                     [
                         "firstname" => $name,
                         "email" => "{$email}"
                     ]
                 ],
             ];

             $data = http_build_query($data);
             $res = $this->corporateGiftHandler->createGift($data);

             if(isset($res['data']) && isset($res['data']['id'])){
                 $responseData = $res['data'];
                 $is_added = @GiftOrder::create(['gift_id'=> $responseData['id'],'gift_number'=> @$responseData['number'], 'app_id' => $app->id, 'product_id' => $product_id, 'status'=> @$responseData['status'], 'api_response'=>$responseData ]);
                 $return = ['status'=> true,'record_id'=> @$is_added->id];
             }else {

                 $return = ['status' => false, 'record_id' => ''];
             }

            // dd($res,$data)
             }

             //return response()->json(['status'=>'','data'=>$res]);
         }
         return response()->json($return);

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
