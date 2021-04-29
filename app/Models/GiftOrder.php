<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftOrder extends Model
{
    protected $fillable = ['object_type','object_id','gift_id', 'gift_number', 'product_id','api_request','api_response','object_id','status','app_id'];
    
    protected $casts = [ 'api_response' => 'json',  'api_request' => 'json'];
}
