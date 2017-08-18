<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission;
use App\Plan;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    public static $plans = null;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

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
        // TODO:变成[]有更深层次的原因，此为临时解决方案
        $key = "role-" . $this->name;
        if (Cache::has($key) && json_encode(Cache::get($key)) != '[]') {
            return Cache::get($key);
        }
        $policies = $this->policies;
        $grouped = [];
        foreach($policies as $key=>$policy) {
            $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
        }
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
}
