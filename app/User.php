<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Traits\VoyagerUser;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Models\Permission;
use Carbon\Carbon;
use App\Policy;
use App\Affiliate;
use App\Jobs\LogAction;
use Psy\Exception\ErrorException;
use Log;
use Illuminate\Support\Collection;
use App\Exceptions\GenericException;

/**
 * @warning Model中使用Log等服务是错误的用法
 */
class User extends Authenticatable
{
    use Notifiable, Billable;

    const TAG_DEFAULT = "default";
    const TAG_WHITELIST = "whitelist";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'regip'
    ];

    public function bookmarks()
    {
        return $this->hasMany('App\Bookmark');
    }

    public function bookmarkItems()
    {
        return $this->hasMany('App\BookmarkItem', 'uid');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function affiliates()
    {
        return $this->hasMany('App\Affiliate', 'email', 'email');
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        return in_array($this->role->name, (is_array($name) ? $name : [$name]));
    }

    public function setRole($name)
    {
        $role = Role::where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }


    /**
     * 当前订阅
     * 用户的当前订阅只能是已经付款且处于活动状态的订阅
     */
    public function subscription()
    {
        return $this->belongsTo('App\Subscription');
    }

    /**
     * 所有订阅
     * 用户可能购买订阅，但是没有付款，获取用户的所有订阅可以更好地通知用户
     */
    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    /**
     * 获取所有支付记录
     */
    public function payments()
    {
        return $this->hasMany('App\Payment', 'client_id');
    }

    public function getUsageAttribute($value)
    {
        // 用户权限的设计
        if (is_null($value)) {
            return [];//$this->initUsageByRole($this->role);
        } 
        /* $items = $this->role->groupedPolicies(); */
        $value = json_decode($value, true);
        // TODO:用户角色的合并，应该是在初始化时，直接合并进入usage中，而不是在获取的时候动态合并。后续优化。
        /* foreach ($items as $key => $item) { */
        /*     //用户权限的默认值不允许重写 */
        /*     if ($this->userCan($key)) */
        /*         continue; */
        /*     $value[$key][0] = $item[0]; */
        /*     $value[$key][1] = $item[1]; */
        /* } */
        return $value;
    }

    public function setUsageAttribute($value)
    {
        $this->attributes['usage'] = json_encode($value);
    }

    /**
     * 初始化usage
     * @param App\Role $role 角色
     * @param boolean $clear 默认情况下，初始化策略如果发现有累计值，就保留。该参数设置为将统一清为0
     */
    public function initUsageByRole(Role $role, $clear = false)
    {
        try {
            $oldUsage = json_decode($this->attributes['usage'], true);
        } catch(\Exception $e) {
            $oldUsage = null;
        }
        $items = $role->groupedPolicies();

            /* Log::debug("key:" . $key); */
        foreach($items as $key=>$item) {
            if (!$clear && isset($oldUsage[$key])) {
                $items[$key] = $oldUsage[$key];
                $items[$key][0] = $item[0];
                $items[$key][1] = $item[1];
            } else {
                $items[$key][2] = 0;
            }
            /* Log::debug("key:" . $key); */
        }
        $this->usage = $items;
        return $items;
    }

    /**
     * 重新初始化usage
     */
    public function reInitUsage($clear = false)
    {
        $oldUsage = $this->usage;
        $usage = $this->initUsageByRole($this->role, $clear);
        if ($usage != $oldUsage)
            $this->save();
    }

    /**
     * 获取指定key的策略使用情况
     */
    public function getUsage($key)
    {
        //没有初始化则根据角色初始化，获取usage时都会先初始化，切换角色也重新初始化，一般不会发生这种事
        if (!array_key_exists($key, $this->usage)) {
            $items = $this->role->groupedPolicies();
            if (!array_key_exists($key, $items)) {
                return null;
            }
            $item = $items[$key];
            $item[2] = 0;
        } else {
            //测试的时候发现，新加入的权限和策略是直接进入到这个else分支，导致$item[2]没有初始化为0，会导致出现
            //下标2未出现的错误。
            $item = $this->usage[$key];
            if(count($item) < 3) {
                $item[2] = 0;
            }
        }
        return $item;
    }

    /**
     * 添加用户独有的策略
     * @remark 添加策略不检查权限，但是在使用上必须检查权限
     */
    public function addUserUsage($key, $defaultValue, $used = 0, $extra = null)
    {
        $policy = Policy::where('key', $key)->first();
        if (!($policy instanceof Policy)) {
            return false;
        }
        $usage = $this->usage;
        $usage[$key] = [$policy->type, $defaultValue, $used];
        if ($extra) {
            $usage[$key][3] = $extra;
        }
        $this->usage = $usage;
        $this->save();
        return true;
    }

    public function updateUsage($key, $used, $extra)
    {
        $usage = $this->usage;
        if (!isset($usage[$key])) 
            return false;
        $usage[$key][2] = $used;
        if (!is_null($extra))
            $usage[$key][3] = $extra;
        $this->usage = $usage;
        $this->save();
        return true;
    }

    public function incUsage($key, $extra)
    {
        $usage = $this->usage;
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage[$key]) > 2 ? $usage[$key][2] + 1 : 1, $extra);
        return true;
    }

    public function getCache($key)
    {
        $newkey = "{$this->id}:" . $key;
        return Cache::get($newkey);
    }

    public function setCache($key, $val)
    {
        $newkey = "{$this->id}:" . $key;
        Cache::put($newkey, $val, 1440);
    }

    public function can($key, $arguments = [])
    {
        //先检查角色
        $count = $this->role->permissions()->where('key', $key)->count();
        if ($count > 0)
            return true;
        //再检查User自身
        return $this->userCan($key);
    }

    public function userCan($key)
    {
        /* $arr = $this->permissions()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->get()->pluck('key')->toArray(); */

        /* if (in_array($key, $arr)) */
        /*     return true; */

        $item = $this->permissions()->where('key', $key)->first();//()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->where('key', $key)->count();
        if (!($item instanceof Permission)) {
            return false;
        }
        /* echo(json_encode($item)); */
        if (!($item->pivot['expired'] == null || (new Carbon($item->pivot['expired']))->gt(Carbon::now()))) {
            return false;
        }
        return true;
    }

    public function getPolicy($key)
    {
        $items = $this->role->groupedPolicies();
        if (!array_key_exists($key, $items)) {
            return null;
        }
    }

    /**
     * 用户权限与角色权限合并
     * @warning 需要注意的是以属性形式表示只会加载一次，如果要在一次执行中做修改，那内部应该改用$this->permissions->all()
     */
    public function getMergedPermissions()
    {
        $rolePermissions = $this->role->permissions;
        $userPermissions = $this->permissions()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->get();
        $merged = $rolePermissions->merge($userPermissions);
        return $merged;
    }

    /**
     * 仅返回user本身的权限，不包含role的权限
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withPivot(['expired']);
    }

    /**
     * 动态添加权限
     */
    public function addPermission($key, $expired = null)
    {
        $permission = Permission::where('key', $key)->first();
        if (!($permission instanceof Permission))
            return false;
        if ($this->permissions()->wherePivot('permission_id', $permission->id)->count() > 0) {
            $this->permissions()->updateExistingPivot($permission->id, ['expired' => $expired]);
            return true;
        }
        $this->permissions()->attach($permission->id, ['expired' => $expired]);
        return true;
    }

    /**
     * 动态删除权限
     */
    public function delPermission($key)
    {
        $permission = Permission::where('key', $key)->first();
        if (!($permission instanceof Permission))
            return false;
        $this->permissions()->detach($permission->id);
        return true;
    }

    /**
     * 检查是否过期
     */
    public function isExpired()
    {
        return $this->role_id != 3 && $this->expired  && $this->expired != '0000-00-00 00:00:00' && Carbon::now()->gt(new Carbon($this->expired));
    }


    /**
     * 重置过期用户为Free，并清空过期时间
     */
    private function resetExpired()
    {
        $role = Role::where('name', 'Free')->first();
        if ($this->role_id == $role->id)
            return false;
        $this->role_id = $role->id;
        $this->expired = null;
        $this->initUsageByRole($role);
        $this->save();
        return true;
    }

    /**
     * 如果过期就重置
     */
    public function resetIfExpired()
    {
        if (!$this->isExpired())
            return false;
        $this->resetExpired();
        dispatch(new LogAction(ActionLog::ACTION_USER_EXPIRED, json_encode(["name" => $this->name, "email" => $this->email, "expired" => $this->expired ]), "", $this->id));
        return true;
    }

    /**
     * 获取最近的有效订阅
     */
    public function getEffectiveSub()
    {
        $subs = $this->subscriptions()->where('status', '<>', Subscription::STATE_CREATED)->orderBy('created_at', 'desc')->get();
        $baseSub = null;
        // 有效订阅未必是最后一条，比如用户取消当前订阅，购买新订阅，但是扣款失败。这时没有活动订阅，有效订阅仍然是前一个。
        for ($i = 0; $i < count($subs); ++$i) {
            if ($subs[$i]->hasEffectivePayment()) {
                $baseSub = $subs[$i];
                break;
            }
        }
        return $baseSub;
    }

    /**
     * 根据订单信息更新用户信息
     * 目前更新角色与过期时间
     */
    public function fixInfoByPayments()
    {
        if ($this->tag == User::TAG_WHITELIST) {
            Log::debug("{$this->email} is in whitelist, ignore");
            return;
        }
        if ($this->role_id < 3) {
            Log::debug("{$this->email} is an admin user, ignore");
            return;
        }
        $baseSub = $this->getEffectiveSub();
        // 没有有效订单，又不在白名单内的用户，同步后它们的权限将重置为Free
        if (!$baseSub)  {
            if ($this->resetExpired()) {
                Log::info("{$this->email} is not a Free user or expired is set,but has no valid payments and not in whitelist, reset it");
            }
            return;       
        }

        $plan = $baseSub->getPlan();
        if (!$plan) {
            Log::error("the subscription has valid plan: {$baseSub->plan}, please check");
            return;
        }
        $role = $plan->role;
        $dirty = false;
        // 角色不一致就重置角色
        if ($this->role_id != $role->id) {
            $this->role_id = $role->id;
            $this->initUsageByRole($role);
            $dirty = true;
            Log::info("{$this->email} role change to {$role->name}, reset usage");
        }

        // 过期时间设置为有效订单的结束时间+1天，一个时间段内只会有一个有效订单
        foreach ($baseSub->payments as $payment) {
            if ($payment->isEffective()) {
                $expired = new Carbon($payment->end_date);
                $expired->addDay(); 
                if ($this->expired != $expired) {
                    Log::info("{$this->email}'s expired is updated:{$this->expired} -> {$expired}");
                    $this->expired = $expired;
                    $dirty = true;
                }
                break;
            }
        }
        if ($dirty)
            $this->save();
    }

    /**
     * 是否在白名单中
     */
    public function inWhitelist()
    {
        return $this->tag == User::TAG_WHITELIST;
    }

    /**
     * 权限由两部分组成：
     * - 权限列表
     * - 策略列表
     * 角色的权限与策略都是在填充时生成的，所以只能在那个时间点检查，通过Role的checkUsage可检查。
     * 用户的权限与策略的检查：
     * - 权限来自角色，所以无需检查
     * - 将Usage与策略对比，有不一致的提示错误
     */
    public function checkUsage()
    {
        $role = $this->role;
        $rawUsage = new Collection(json_decode($this->attributes['usage'], true));
        if (!$role) {
            Log::warning("{$this->email} has no role, role id:{$this->role_id}");
            throw new GenericException($this, "({$this->email}) has no valild:{$this->role_id})", 1000);
        }
        foreach ($role->groupedPolicies()  as $key => $policy) {
            $usage = $rawUsage->get($key);
            if (!$usage || $usage[0] != $policy[0] || $usage[1] != $policy[1]) {
                throw new GenericException($this, "({$this->email})should be: $key-" . json_encode($policy) . ", but now is : " . ($usage ? json_encode($usage) : "no usage"), 1000);
            }
        }
        return true;
    }

    public function dumpUsage($print)
    {
        foreach ($this->usage as $key => $value) {
            call_user_func($print, "$key:" . json_encode($this->getUsage($key)));
        }
    }
}
