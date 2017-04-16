<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    const TYPE_USERS = "USERS";
    const TYPE_USER_LOGIN = "USER_LOGIN";
    const TYPE_USER_LOGOUT = "USER_LOGOUT";
    const TYPE_USER_REGISTERED = "USER_REGISTERED";
    /* const TYPE_AD_SEARCH = "AD_SEARCH"; */
    /* const TYPE_AD_KEYWORD = "AD_KEYWORD"; */
    /* const TYPE_ADSER_SEARCH =  "ADSER_SEARCH"; */
    //
    //
    /* public static function log($type, $param, $remark, $uid, $ip) */ 
    /* { */
    /*     /1* if ($ip == null) *1/ */ 
    /*     /1*     $ip = Request()->ip(); *1/ */
    /*     $action = new ActionLog(); */
    /*     /1* if ($uid < 0 && Auth::user()) { *1/ */
    /*     /1*     $uid = Auth::user()->id; *1/ */
    /*     /1* } *1/ */
    /*     $action->user_id = $uid; */
    /*     $action->type = $type; */
    /*     $action->ip = $ip; */
    /*     $action->param = $param; */
    /*     $action->remark = $remark; */
    /*     $action->save(); */
    /* } */
}
