<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission;
use App\Plan;

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
        $policies = $this->policies;
        $grouped = [];
        foreach($policies as $key=>$policy) {
            $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
        }
        return $grouped;
    }
}
