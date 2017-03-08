<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission;
use Braintree\Plan;

class Role extends Model
{
    public static $plans = null;
    protected $guarded = [];

    static public function plans()
    {
        if (self::$plans == null) {
            self::$plans = Plan::all();
        }  
        return self::$plans;
    }
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
     * 从Braintree获取该角色的计划
     */
    public function getPlanAttribute()
    {
        $plans = $this->plans();
        $res = [];
        $type = "annually";
        for($i=0;$i<count($plans);++$i) {
            if (preg_match('/^' . $this->name . '/', $plans[$i]->id) > 0) {
                $plan = $plans[$i];
                if ($plan->id == $this->name . "_Monthly")
                    $type = "monthly";
                $res[$type] = ["id"=>$plan->id, "price"=>$plan->price, "billingFrequency"=>$plan->billingFrequency];
            }
        }    
        if (count($res) > 0)
            return $res; 
        return null;
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
