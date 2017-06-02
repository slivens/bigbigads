<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{

    protected $fillable = [
        'email', 'password', 'telephone', 'address', 'track', 'status', 'type', 'click', 'action', 'share', 'balance'
    ];

    protected $hidden = [
        'password'
    ];

    public function users()
    {
        return $this->hasMany('App\User');
    }   
}
