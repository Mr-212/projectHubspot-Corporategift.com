<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = 'app';


    protected $fillable = ['hub_app_id','hub_id','hub_user','hub_user_id','hub_access_token','hub_refresh_token','hub_expires_id', 'corporate_gift_token','is_active', 'request_data'];

    protected $casts = [ 'request_data' => 'json' ];
}
