<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class VerifyHubspotSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $body = null;
        //Log::channel('HubSpotCrmCardLog')->info('BODY: '.json_encode($body));
        $method = $request->getMethod();
//        if($request->hasHeader('X-Hubspot-Signature')) {
            $signature = @$request->header('X-Hubspot-Signature');
            $url = url('/') . $request->getRequestUri();
            if($request->isMethod('POST'))
                $body = $request->getContent();

            $isVerified = $this->verifySignature($signature, $method, $url, $body);
            if(!$isVerified){
                return response()->json(['error'=>true, 'message'=>'Signature not Verified.']);
            }
//        }

        return $next($request);
    }


    public function verifySignature($signature,$method,$url,$body){
        $verified = false;
        $strtoMatch = Config::get('constants.hubspot.client_secret').$method.$url;

        if(!empty($body))
            $strtoMatch.=$body;
        $sigToMatch = hash('sha256',$strtoMatch);

        if($signature == $sigToMatch)
            $verified = true;

        Log::channel('HubSpotCrmCardLog')->info('Signature to match: '.$signature);
        Log::channel('HubSpotCrmCardLog')->info('Signature1: '.$sigToMatch);

        return $verified;

    }
}
