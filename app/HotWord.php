<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotWord extends Model
{
    //
    protected $fillable = [
        'keyword', 'status', 'type'
    ];
}
