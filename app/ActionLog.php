<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    /**
     *  用户相关
     */
    const ACTION_USER_LOGIN = "USER_LOGIN";
    const ACTION_USER_LOGIN_MOBILE = "USER_LOGIN_MOBILE";
    const ACTION_USER_LOGOUT = "USER_LOGOUT";
    const ACTION_USER_REGISTERED = "USER_REGISTERED";
    const ACTION_USER_REGISTERED_MOBILE = "USER_REGISTERED_MOBILE";
    const ACTION_USER_BIND_SOCIALITE_MOBILE_BASE = "USER_BIND_SOCIALITE_MOBILE_BASE";
    const ACTION_USER_BIND_SOCIALITE_BASE = "USER_BIND_SOCIALITE_BASE";
    const ACTION_USER_EXPIRED = "USER_EXPIRED";

    /**
     * 权限相关
     */
    const ACTION_ROLE_MANUAL_CHANGE = "ROLE_MANUAL_CHANGE";

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
    const ACTION_ATTACK_RATE = "ATTACK_RATE";

    /*
     * 收藏夹记录
     */
    const ACTION_SEARCH_INIT_PERDAY_BOOKMARK = "SEARCH_INIT_PERDAY_BOOKMARK";
    const ACTION_SEARCH_LIMIT_PERDAY_BOOKMARK = "SEARCH_LIMIT_PERDAY_BOOKMARK";

    /**
     * 支付系统
     */
    const ACTION_USER_CANCEL = "USER_CANCEL";
    const ACTION_AUTO_CANCEL = "AUTO_CANCEL";
    const ACTION_ADMIN_CANCEL = "ADMIN_CANCEL";
    const ACTION_USER_REQUEST_REFUND = "USER_REQUEST_REFUND";

    /*
     * 移动端行为记录
     */
    const ACTION_MOBILE_USER_CURRENT        = 'MOBILE_USER_CURRENT';
    const ACTION_MOBILE_OWNER_SEARCH        = 'MOBILE_OWNER_SEARCH';
    const ACTION_MOBILE_OWNER_SEARCH_NULL   = 'MOBILE_OWNER_SEARCH_NULL';
    const ACTION_MOBILE_OWNER_LOADMORE      = 'MOBILE_OWNER_LOADMORE';
    const ACTION_MOBILE_OWNER_ANALYSIS      = 'MOBILE_OWNER_ANALYSIS';
    const ACTION_MOBILE_AD_ANALYSIS         = 'MOBILE_AD_ANALYSIS';

    /*
     * 记录用户点击欢迎页面CONTINUE按钮
     */
    const ACTION_RECORD_CLICK_CONTINUE = "RECORD_CLICK_CONTINUE";

    /*
     * 请求无权限过滤行为记录
     */
    const ACTION_USER_REQUEST_FILTER = "USER_REQUEST_FILTER";
}
