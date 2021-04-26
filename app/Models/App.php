<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = 'app';


    protected $fillable = ['identifier','hub_app_id','hub_id','hub_user','hub_user_id','hub_access_token','hub_refresh_token','hub_expires_in', 'corporate_gift_token','is_active', 'request_data',
   'unique_app_id','user_id' ];

    protected $casts = [ 'request_data' => 'json' ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
