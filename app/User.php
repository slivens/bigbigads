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

class User extends Authenticatable
{
    use Notifiable, Billable;

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

    public function aff()
    {
        return $this->belongsTo('App\Affiliate', 'aff_id');
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


    public function subscription()
    {
        return $this->belongsTo('App\Subscription');
    }

    public function getUsageAttribute($value)
    {
        if (is_null($value)) {
            return $this->initUsageByRole($this->role);
        } 
        $items = $this->role->groupedPolicies();
        $value = json_decode($value, true);
        foreach ($items as $key => $item) {
            //用户权限的默认值不允许重写
            if ($this->userCan($key))
                continue;
            $value[$key][0] = $item[0];
            $value[$key][1] = $item[1];
        }
        return $value;
    }

    public function setUsageAttribute($value)
    {
        $this->attributes['usage'] = json_encode($value);
    }

    public function initUsageByRole($role)
    {
        $items = $role->groupedPolicies();
        foreach($items as $key=>$item) {
            $items[$key][2] = 0;
        }
        $this->usage = $items;
        return $items;
    }

    public function getUsage($key)
    {
        //没有初始化则从根据角色初始化，获取usage时都会先初始化，切换角色也重新初始化，一般不会发生这种事
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
}
