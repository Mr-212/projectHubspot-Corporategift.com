<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function hubspot_contact(Request $request){
        Log::info($request->all());
    }
}
