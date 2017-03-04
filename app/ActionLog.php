<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    const TYPE_USERS = "users";
    const TYPE_AD_SEARCH = "ad-search";
    const TYPE_ADSER_SEARCH =  "adser-search";
    //
    //
    public static function log($type, $param, $remark = "", $uid = -1, $ip = null) 
    {
        if ($ip == null) 
            $ip = Request()->ip();
        $action = new ActionLog();
        if ($uid < 0 && Auth::user()) {
            $uid = Auth::user()->id;
        }
        $action->user_id = $uid;
        $action->type = $type;
        $action->ip = $ip;
        $action->param = $param;
        $action->remark = $remark;
        $action->save();
    }
}
