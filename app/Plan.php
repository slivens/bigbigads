<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    //
    protected $fillable = ['name', 'display_name', 'display_order', 'type', 'frequency', 'frequency_interval', 'cycles', 'amount', 'currency'];
}
