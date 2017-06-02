<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateLog extends Model
{
    protected $fillable = ['ip', 'track'];
}
