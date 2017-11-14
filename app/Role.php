<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission;
use App\Plan;
use App\Exceptions\GenericException;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    public static $plans = null;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    protected $permissionKeys = null;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }   
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function policies() 
    {
        return $this->belongsToMany(Policy::class)->withPivot('value');
    }

    /**
     * 获取该角色的计划
     * @return annually和monthly
     */
    public function getPlansAttribute()
    {
        $annually = Plan::where('name', '=', $this->plan)->first();
        $monthly = Plan::where('name', '=', "{$this->plan}_monthly")->first();
        return ["annually" => $annually, "monthly" => $monthly];
    }

    /**
     * 将策略按key组织，只返回必要信息
     * usage的格式:[key] = [类型，默认值,当前值,额外信息(时间)]
     */
    public function groupedPolicies()
    {
        $key = "role-" . $this->name;
        return Cache::get($key, []);
    }


    /**
     * 按照[key] => [type, value]的方式组织Policy
     */
    public function generateGroupedPolicies()
    {
        $policies = $this->policies;
        $grouped = [];
        foreach($policies as $key=>$policy) {
            $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
        }
        return $grouped;
    }

    /**
     * 生成缓存
     * usage的格式:[key] = [类型，默认值,当前值,额外信息(时间)]
     * @remark 参考设计说明，生成缓存应该在种子填充阶段完成
     */
    public function generateCache()
    {
        $key = "role-" . $this->name;
        $grouped = $this->generateGroupedPolicies();
        Cache::forever("role-". $this->name, $grouped);
        return $grouped;
    }

    /**
     * 清除缓存
     */
    public function cleanCache()
    {
        Cache::forget("role-" . $this->name);
    }

    /**
     * 检查Usage的缓存是否正确（非常重要）
     */
    public function checkCacheUsage()
    {
        $key = "role-" . $this->name;
        $cache = Cache::get($key, null);
        if ($cache === null)
            throw new GenericException($this, "{$this->name}:role usage cache is null");
        foreach($this->policies as $policy) {
            $key = $policy->key;
            $right = [$policy->type, $policy->pivot->value];
            if (!isset($cache[$key]) || $cache[$key] != $right)
                throw new GenericException($this, "{$this->name}:$key should be " . json_encode($right) . "but result is " . (isset($cache[$key]) ? $cache[$key] : " not set"));
        }   
        return true;
    }

    /**
     * 打印出角色的权限与策略情况，主要方便排查问题
     */
    public function dumpUsage($print)
    {
        $key = "role-" . $this->name;
        $cache = Cache::get($key, null);
        if ($cache === null)
            throw new GenericException($this, "{$this->name}:role usage cache is null");
        foreach($cache as $key => $policy) {
            call_user_func($print, "{$key}:" . json_encode($policy));
        }
    }

    /**
     * 检查角色是否有指定权限
     * @param $name 权限名称
     * @return bool true有权限,false无权限
     */
    public function can($name) {
        if (!$this->permissionKeys)
            $this->permissionKeys = $this->permissions->pluck('key')->toArray();
        return in_array($name, $this->permissionKeys);
    }
}
