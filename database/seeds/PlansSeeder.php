<?php

use Illuminate\Database\Seeder;
use App\Plan;
use App\Role;

/**
 * 全部通过Voyager后台操作，禁止再使用该Seeder文件导入
 * @deprecated
 */
class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            //plan的name默认以年为单位，如果以月，周，日为单位，必须加后缀
            //_monthly,_weekly,_daily
            //下面的type固定为REGULAR,cycles固定为0，循环支付的要求
            $plans = [
                [
                    "name" => "free",
                    "display_name" => "Free Level",
                    "desc" => "Free Plan",
                    "display_order" => 0, 
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,//can't be null or 0 if type is TRIAL
                    "amount" => 0,
                    "currency" => "USD",
                    "role"=> "Free",
                    "slug"=> "free"
                ],
                [
                    "name" => "free_monthly",
                    "display_name" => "Free Level",
                    "desc" => "Free Plan for an month",
                    "display_order" => 0, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,//can't be null or 0 if type is TRIAL
                    "amount" => 0,
                    "currency" => "USD",
                    "role"=> "Free",
                    "slug"=> "free_monthly"
                ],
                [
                    "name" => "lite_monthly",
                    "display_name" => "Lite Monthly",
                    "desc" => "Lite Plan for one month",
                    "display_order" => 1, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 59.99,
                    "currency" => "USD",
                    "role" => "Lite",
                    "slug"=> "lite_monthly"
                ],
                [
                    "name" => "lite_quarterly",
                    "display_name" => "Lite Quarterly",
                    "desc" => "Lite Plan for three month",
                    "display_order" => 1, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 3,
                    "cycles" => 0,
                    "amount" => 119.97,
                    "currency" => "USD",
                    "role" => "Lite",
                    "slug"=> "lite_quarterly"
                ],
                [
                    "name" => "lite_annual",
                    "display_name" => "Lite Annual",
                    "desc" => "Lite Plan for one year",
                    "display_order" => 1, 
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 299.88,
                    "currency" => "USD",
                    "role" => "Lite",
                    "slug"=> "lite_annual"
                ],
                [
                    "name" => "standard_monthly",
                    "display_name" => "Standard Monthly",
                    "desc" => "standard  Plan for one month",
                    "display_order" => 2, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 99,
                    "currency" => "USD",
                    "role" => "Standard",
                    "slug"=> "standard_monthly"
                ],
                [
                    "name" => "standard_quarter_monthly",
                    "display_name" => "Standard Quarterly",
                    "desc" => "standard  Plan for three months",
                    "display_order" => 2, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 3,
                    "cycles" => 0,
                    "amount" => 237,
                    "currency" => "USD",
                    "role" => "Standard",
                    "slug"=> "standard_quarterly"
                 ],
                [
                    "name" => "standard",
                    "display_name" => "Standard Annual",
                    "desc" => "standard  Plan for one year",
                    "display_order" => 2, 
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 780,
                    "currency" => "USD",
                    "role" => "Standard",
                    "slug"=> "standard_annual"
                ],
                [
                    "name" => "advanced_monthly",
                    "display_name" => "Plus Plan",
                    "desc" => "Plus Plan for one month",
                    "display_order" => 3, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 169,
                    "currency" => "USD",
                    "role" => "Advanced",
                    "slug"=> "advanced_monthly"
                ],
                [
                    "name" => "advanced",
                    "display_name" => "advanced Plan",
                    "desc" => "advanced Plan for one year",
                    "display_order" => 3, 
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 1668,
                    "currency" => "USD",
                    "role" => "Advanced",
                    "slug"=> "advanced"
                ],
                [
                    "name" => "vip_monthly",
                    "display_name" => "vip Plan",
                    "desc" => "vip Plan for one month",
                    "display_order" => 4, 
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 299,
                    "currency" => "USD",
                    "role" => "Pro",
                    "slug"=> "vip_monthly"
                ],
                [
                    "name" => "vip",
                    "display_name" => "vip Plan",
                    "desc" => "vip Plan for one year",
                    "display_order" => 4, 
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 3588,
                    "currency" => "USD",
                    "role" => "Pro",
                    "slug"=> "vip"
                ]
            ];

            //每次填充都会清空所有计划
            Plan::where('id', '>', 0)->delete();
            foreach ($plans as $key=>$item) {
                $role = Role::where("name", $item["role"])->first();
                if ($role instanceof Role) {
                    $item["role_id"] = $role->id;
                }
                unset($item["role"]);
                Plan::create($item);
            }
            echo "insert plans\n";

            //将计划绑定到对应的角色上，先清空再绑定
            Role::where('id', '>', 2)->update(['plan' => null]);
            $roles = ["Free" => "free", "Lite" => "lite", "Standard" => "standard", "Advanced" => "advanced", "Pro" => "vip"];
            foreach ($roles as $key => $item) {
                $role = Role::where('name', $key)->first();
                $role->plan = $item;
                $role->save();
            }
            echo "binding plans to roles\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
