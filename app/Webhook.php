<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $casts = [
        'webhook_content' => 'json'
    ];
}
