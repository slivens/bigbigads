<?php

use Illuminate\Database\Seeder;
use App\Plan;
use App\Role;

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
            $plans = [
                [
                    "name" => "free",
                    "display_name" => "Free",
                    "type" => "TRIAL",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 0,
                    "currency" => "USD"
                ],
                [
                    "name" => "start_monthly",
                    "display_name" => "Start Plan",
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 10,
                    "currency" => "USD"
                ],
                [
                    "name" => "start",
                    "display_name" => "Start Plan",
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 100,
                    "currency" => "USD"
                ],
                [
                    "name" => "standard_monthly",
                    "display_name" => "Standard Plan",
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 20,
                    "currency" => "USD"
                ],
                [
                    "name" => "standard",
                    "display_name" => "Standard Plan",
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 200,
                    "currency" => "USD"
                ],
                [
                    "name" => "advanced_monthly",
                    "display_name" => "Advanced Plan",
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 30,
                    "currency" => "USD"
                ],
                [
                    "name" => "advanced",
                    "display_name" => "Advanced Plan",
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 300,
                    "currency" => "USD"
                ],
                [
                    "name" => "vip_monthly",
                    "display_name" => "Vip Plan",
                    "type" => "REGULAR",
                    "frequency" => "MONTH",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 50,
                    "currency" => "USD"
                ],
                [
                    "name" => "vip",
                    "display_name" => "VIP Plan",
                    "type" => "REGULAR",
                    "frequency" => "YEAR",
                    "frequency_interval" => 1,
                    "cycles" => 0,
                    "amount" => 500,
                    "currency" => "USD"
                ]
            ];

            //每次填充都会清空所有计划
            Plan::where('id', '>', 0)->delete();
            foreach($plans as $key=>$item) {
                Plan::create($item);
            }
            echo "insert plans\n";

            //将角色绑定到对应的计划上
            $roles = ["Free" => "free", "Standard" => "standard", "Advanced" => "advanced", "Pro" => "vip"];
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
