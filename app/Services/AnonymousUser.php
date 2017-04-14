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
        $user = null;//Cache::get($ip);
        if (!is_null($user) && !is_null($user->date) && $user->date->isToday()) {
            //TODO:记录登陆动作
            Log::debug("$ip is still valid");
        } else {
            $role = Role::where('name', 'Free')->with('permissions', 'policies')->first();
            $user = new AnonymousUser();
            $user->id = 0;
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
    public function updateUsage($key, $used, $extra)
    {
        $usage = &$this->usage;
        if (!isset($usage[$key])) 
            return false;
        $usage[$key][2] = $used;
        if (!is_null($extra))
            $usage[$key][3] = $extra;
        Cache::put($this->ip, $this, 1440);
        return true;
    }

    public function getUsage($key)
    {
        $item = $this->usage[$key];
        if (!isset($item[2]))
            $item[2] = 0;
        return $item;
    }

    public function incUsage($key, $extra) 
    {
        $usage = &$this->usage;
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage[$key]) > 2 ? $usage[$key][2] + 1 : 1, $extra);
        return true;
    }

    public function getCache($key)
    {
        $newkey = $this->ip . $key;
        return Cache::get($newkey);
    }

    public function setCache($key, $val)
    {
        $newkey = $this->ip . $key;
        Cache::put($newkey, $val, 1440);
    }

    public function can($key)
    {
        $count = $this->role->permissions()->where('key', $key)->count();
        if ($count > 0)
            return true;
        return false;
    }
}
