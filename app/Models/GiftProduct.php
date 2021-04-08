<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftProduct extends Model
{
    use HasFactory;

    protected $table = 'gift_products';
    protected $fillable = ['app_id', 'product_id','data'];

    protected $casts = [ 'data' => 'json' ];
}
