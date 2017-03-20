<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    //
    protected $fillable = ['name', 'display_name', 'type', 'frequency', 'frequency_interval', 'cycles', 'amount', 'currency'];
}
