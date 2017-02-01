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

/*     public function getMonthlyPlanAttribute() */
/*     { */
/*         $plans = $this->plans(); */
/*         for($i=0;$i<count($plans);++$i) { */
/*             if ($plans[$i]->id == $this->name . "_Monthly") { */
/*                 $plan = $plans[$i]; */
/*                 return ["id"=>$plan->id, "price"=>$plan->price, "billingFrequency"=>$plan->billingFrequency]; */
/*             } */
/*         } */    
/*         return null; */ 
/*     } */
}
