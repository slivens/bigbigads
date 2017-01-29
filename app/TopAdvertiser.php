<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopAdvertiser extends Model
{
    protected $table = "ads_analysis.view_top_advertiser";//经过测试，需要同时指定数据库，否则还是查找
    protected $connection = "rankdb";
}
