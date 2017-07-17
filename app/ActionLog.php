<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    // 登陆相关
    const ACTION_USER_LOGIN = "USER_LOGIN";
    const ACTION_USER_LOGOUT = "USER_LOGOUT";
    const ACTION_USER_REGISTERED = "USER_REGISTERED";

    /**
     * 每日搜索次数(用户使用热词时，由单独的热词统计次数)
     */
    const ACTION_SEARCH_TIMES_PERDAY = "SEARCH_TIMES_PERDAY";
    /**
     * 热词的每日搜索次数
     */
    const ACTION_HOT_SEARCH_TIMES_PERDAY = "HOT_SEARCH_TIMES_PERDAY";

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
