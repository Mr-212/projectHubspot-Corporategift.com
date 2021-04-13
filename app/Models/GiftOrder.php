<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftOrder extends Model
{
    protected $fillable = ['gift_id', 'gift_number', 'product_id','api_request','api_response','object_id','status','app_id'];

}
