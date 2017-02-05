<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Role;
use Carbon\Carbon;
use Log;

class AnonymousUser 
{
    public $role;
    public $usage;
    public $date = null;
    protected $ip;

    /**
     * 返回匿名帐户
     */
    public static function user($req)
    {
        $ip = $req->ip();
        $user = Cache::get($ip);
        if (!is_null($user->date) && $user->date->isToday()) {
            //TODO:记录登陆动作
            Log::debug("$ip is still valid");
        } else {
            $role = Role::where('name', 'Free')->with('permissions', 'policies')->first();
            $user = new AnonymousUser();
            $user->role = $role;
            $user->usage = $role->groupedPolicies();
            $user->date  = Carbon::now();
            $user->ip = $ip;
            Cache::put($ip, $user, 1440);//缓存一天
            Log::debug("$ip is new");
        }
        return $user;
    }
    
    /**
     * 更新匿名用户的资源使用
     */
    public function updateUsage($key, $used)
    {
        $usage = $this->usage;
        if (!isset($usage[$key])) 
            return false;
        $usage[$key][2] = $used;
        Cache::put($this->ip, $this, 1440);
        return true;
    }

    public function incUsage($key) 
    {
        $usage = $this->usage;
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage[$key]) > 2 ? $usage[$key][2] + 1 : 1);
        return true;
    }
}
