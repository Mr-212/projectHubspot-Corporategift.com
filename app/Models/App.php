<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = 'app';


    protected $fillable = ['hubspot_app_id', 'corporate_gift_token','is_active', 'request_data'];

    protected $casts = [ 'request_data' => 'json' ];
}
