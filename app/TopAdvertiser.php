<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopAdvertiser extends Model
{
    protected $table = "ads_analysis.view_top_advertiser";
    protected $connection = "rankdb";
}
