<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\GiftOrder;
use App\Models\GiftProduct;
use App\Services\Hubspot\HubspotConnector;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\CorporateGiftApiHandle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery\Exception;
use Illuminate\Support\Facades\Session;
use App\Utilities\HubspotUtility;

class HupSpotServiceController extends Controller
{
    private $corporateGiftHandler;
    private $hubspotUtility;
    public function __construct()
    {
        $this->corporateGiftHandler = new CorporateGiftApiHandle(null,Config::get('constants.cg_settings.domain_uri'));
        $this->hubspotUtility = new HubspotUtility();

    }

     /**
     * Helper function to get instance of Modal\App class 
     *params : identifier 
     * @return Modal\App::class
     */

    public function getAppByIdentifier($identifier){
       return App::where('identifier',"{$identifier}")->first();
    }


     /**
     * Helper function to set corporategift token to connector class
     *
     * @return void
     */

    public function getCorporateGiftConnector($corporateGiftToken){

       if(!empty($corporateGiftToken))
            $this->corporateGiftHandler->setAccessToken($corporateGiftToken);
        else
            return response()->json(['message' =>'Session expired please refresh the page']);
    } 
 
    /**
     * Hubspot Authentication process from dashboard when clicked in "Coonect to Hubspot icon".
     *
     * @return \Illuminate\Http\Response
     */
    public function hupspot_auth_token_generator(Request $request)
    {
        $res = [];
        try {
            $code = $request->get('code');
            $res = $this->hubspotUtility->authenticate($code);
            if($res['error'] == false)
                return redirect('/dashboard');
        }
        catch(Exception $e) {

        }
        return $res;

    }


    public function post_corporate_gift_token(Request $request){
        $res = ['status' => false, 'message' => 'Token not updated.' ];
        if($request->has('identifier')  && $request->has('corporate_gift_token')){
            try {
                $identifier = $request->get('identifier');
                $corporate_gift_token = $request->get('corporate_gift_token');
                $appExist = App::where('identifier', $identifier)->first();
                if ($appExist) {
                    $appExist->update(['corporate_gift_token' => $corporate_gift_token]);
                    $res = ['status' => true, 'message' => 'Token updated successfully.'];
                    Session::flash('flash_message', 'Coporategift access token updated successfully.');
                    Session::flash('flash_type', 'alert-success');
//                   
                }
            }catch (Exception $e){
                return redirect()->back();
            }
        }
        return redirect()->back();
        // return view('auth.after_verification',compact('res'));
    }


    /**
     * Refresh access token
     * Check expiry date
     * @return \Illuminate\Http\Response
     */


    public function refresh_access_token(Request $request){
        $identifier = $request->get('identifier');
        try {
              $res = $this->hubspotUtility->refresh_access_token($identifier);
              if(!$res['error']){
                  
              }else{

              }
              Session::flash('flash_message', $res['message']);
	          Session::flash('flash_type', $res['alert_type']);
    
        }
        catch(Exception $e) {
        }  
       return redirect()->back();
    }



 /*-------------------------------------------------------------------
     * Helper function used in Hubspot default fetch request,and updated indetifier on each contact time contact page is refreshed
     * pareams: hub_id, user_id of Hubspot portal
     * @return App Modal object
     ------------------------------------------------------------------------*/


    public function getAppByHubIdUserId($hub_id, $userId){

        $identifier = null;
        $app = null;
        if(!empty($hub_id) && !empty($userId)) {
            $app = App::where(['hub_id' => $hub_id, 'hub_user_id' => $userId])->latest()->first();
            if ($app) {
                if(cache()->has($app->identifier)){
                    cache()->delete($app->identifier);
                }
                $newIdentifier = hash('sha256',$app->identifier.$hub_id.$userId);
                if($app->update(['identifier' => $newIdentifier]))
                    $identifier = $newIdentifier;
            }
        }
        return $app;
}


   /*-------------------------------------------------------------------
     * Hubspot fetch request set
     * Whenever any contacts or leads will view crm will send request
     * @return \Illuminate\Http\Response
     ------------------------------------------------------------------------*/

    public function hupspot_data_fetch_request(Request $request){
        //Log::info($request->all());
        $identifier = null;
        $app = null;
        if($request->has('userId') && $request->has('portalId')) {
            $app = $this->getAppByHubIdUserId($request->get('portalId'),$request->get('userId'));
           $identifier = $app->identifier;
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

        //cache()->put($identifier,$params);
        $getGifts = GiftOrder::where(['app_id' => $app->id,'object_id'=>$request->get('associatedObjectId'),'object_type'=>$request->get('associatedObjectType')])->orderBy('created_at','desc')->limit(10)->get();
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
       

        //Primaryaction create gift
        $gift_arr['primaryAction']['type']='IFRAME';
        $gift_arr['primaryAction']['width']=1100;
        $gift_arr['primaryAction']['height']=748;
        $gift_arr['primaryAction']['uri'] = url('/')."/get_all_gift_products?".http_build_query($params);;
        $gift_arr['primaryAction']['label']='View Gift Products';
        return  json_encode($gift_arr);
    }


     /*-------------------------------------------------------------------
     * Hubspot get gift by identifier, controller scoped function
     * @return \Illuminate\Http\Collection
     ------------------------------------------------------------------------*/


    public function getGiftProducts($identifier){
        $CorporateGiftGet = null;
        $app = $this->getAppByIdentifier($identifier);
        if($app && !empty($app->corporate_gift_token)) {
           $this->getCorporateGiftConnector($app->corporate_gift_token);
            $CorporateGiftGet = @GiftProduct::where('app_id',$app->id)->get()->toArray();
            if (empty($CorporateGiftGet)) {
                $CorporateGiftGet = $this->corporateGiftHandler->getGiftProducts();
                if (isset($CorporateGiftGet['status']) && $CorporateGiftGet['status']) {
                    foreach ($CorporateGiftGet['data'] as $data) {
                        GiftProduct::updateOrCreate(['app_id'=>$app->id,'product_id'=>$data['id']], ['product_id' => $data['id'], 'data' => $data]);
                    }
                }
                $CorporateGiftGet = GiftProduct::where('app_id',$app->id)->get()->toArray();
            }
        }

        return $CorporateGiftGet;
    }
   
 


   /*-------------------------------------------------------------------
     * Hubspot get list of all gift products
     * Show popup and list of all gifts when clicked on "view all gifts" button
     * @return \Illuminate\Http\Response
     ------------------------------------------------------------------------*/

     public function get_all_gift_products(Request $request){
         $params = $request->get('params');
         $email = @$request->get('email');
         $name = @$request->get('name');
         $identifier = @$params['identifier'];

        if($identifier) {
             $gift_products = $this->getGiftProducts($identifier);
             return view('hubspot.gift_products', compact('gift_products', 'params'))->render();

        }else{
             return response()->json('Not Verified');
        }
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

      /*-------------------------------------------------------------------
     * Hubspot send gift action modal
     * prepare and send gift after entering subject and message in send gift mdal in Hubspot
     * @return \Illuminate\Http\Response
     ------------------------------------------------------------------------*/

     public function post_hubspot_send_gift_request(Request $request){
         //$params = cache()->get($request->get('identifier'));
        
         $form = $request->all();
         $return = ['status'=>false,'data'=>($request->all())];

         $identifier = @$request['identifier'];
         $subject = @$form['subject'];
         $email =  @$form['email'];
         $name = @$form['name'];
         $object_id = @$form['object_id'];
         $object_type = @$form['object_type'];
         $product_id = $form['product_id'];
        
         if(!empty($identifier) && $subject && $email && $product_id) {
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
                 "sender_name" => "{$app->hub_user}",
                 "recipients" => [
                     [
                         "firstname" => "{$name}",
                         "email" => "{$email}"
                     ]
                 ],
             ];

             $data = http_build_query($data);
             $res = $this->corporateGiftHandler->createGift($data);

             if(isset($res['data']) && isset($res['data']['id'])){
                 $responseData = $res['data'];
                 $is_added = @GiftOrder::create(
                     ['object_id' => $object_id, 
                     'object_type' => $object_type, 
                     'gift_id'=> $responseData['id'],
                     'gift_number'=> @$responseData['number'], 
                     'app_id' => $app->id, 
                     'product_id' => $product_id, 
                     'status'=> @$responseData['status'], 
                     'api_response'=>$responseData,
                     'api_request' =>  $data,
                     ]);
                 $return = ['status'=> true,'record_id'=> @$is_added->id];
             }else {
                 $return = ['status' => false, 'record_id' => ''];
             }
             }
         }
         return response()->json($return);
     }

}
