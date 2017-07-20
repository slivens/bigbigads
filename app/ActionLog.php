<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    // 登陆相关
    const ACTION_USER_LOGIN = "USER_LOGIN";
    const ACTION_USER_LOGIN_MOBILE = "USER_LOGIN_MOBILE";
    const ACTION_USER_LOGOUT = "USER_LOGOUT";
    const ACTION_USER_REGISTERED = "USER_REGISTERED";
    const ACTION_USER_REGISTERED_MOBILE = "USER_REGISTERED_MOBILE";
    const ACTION_USER_BIND_SOCIALITE_MOBILE_BASE = "USER_BIND_SOCIALITE_MOBILE_BASE";
    const ACTION_USER_BIND_SOCIALITE_BASE = "USER_BIND_SOCIALITE_BASE";

    /**
     * 每日搜索次数(用户使用热词时，由单独的热词统计次数)
     */
    const ACTION_SEARCH_TIMES_PERDAY = "SEARCH_TIMES_PERDAY";
    const ACTION_SEARCH_INIT_PERDAY = "SEARCH_INIT_PERDAY";
    const ACTION_SEARCH_LIMIT_PERDAY = "SEARCH_LIMIT_PERDAY";
    const ACTION_SEARCH_WHERE_CHANGE_PERDAY = "SEARCH_WHERE_CHANGE_PERDAY";

    /*
     * 每日搜索特定广告主次数
     */
    const ACTION_SEARCH_INIT_PERDAY_ADSER = "SEARCH_INIT_PERDAY_ADSER";
    const ACTION_SEARCH_WHERE_CHANGE_PERDAY_ADSER = "SEARCH_WHERE_CHANGE_PERDAY_ADSER";
    const ACTION_SEARCH_LIMIT_PERDAY_ADSER = "SEARCH_LIMIT_PERDAY_ADSER";

    /*
     * 热词的每日搜索次数
     */
    const ACTION_HOT_SEARCH_TIMES_PERDAY = "HOT_SEARCH_TIMES_PERDAY";

    /*
     * 恶意攻击记录
     */
    const ACTION_ATTACK_IP_RATE = "ATTACK_IP_RATE";

    /*
     * 收藏夹记录
     */
    const ACTION_SEARCH_INIT_PERDAY_BOOKMARK = "SEARCH_INIT_PERDAY_BOOKMARK";
    const ACTION_SEARCH_LIMIT_PERDAY_BOOKMARK = "SEARCH_LIMIT_PERDAY_BOOKMARK";

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
