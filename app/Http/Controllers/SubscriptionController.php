<?php

namespace App\Http\Controllers;
use Braintree\ClientToken;
use Braintree\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Log;

class SubscriptionController extends Controller
{
    /**
     * 显示支付表单
     */
    public function form(Request $req)
    {
        $clientToken = ClientToken::generate();
        $planid = $req->plan;
        $plans = Plan::all();
        foreach ($plans as $item) {
            if ($item->id == $planid)
                $plan = $item;
        }
        return view('subscriptions.pay', ['clientToken'=>$clientToken, 'plan'=>$plan]);
    }

    /**
     * 支付表单提示的处理
     */
   public function pay(Request $req)
    {
        //check that we have nonce and plan in the incoming HTTP request
        if( empty($req->input( 'payment-method-nonce' ) ) || empty( $req->input('plan') ) ){
            return redirect()->back()->withErrors(['message' => 'Invalid request']);
        }
        $user = Auth::user();
        /* try { */
            $subscription = $user->newSubscription('main', $req->plan )->create( Input::get( 'payment-method-nonce' ), [
                'email' => $user->email
            ]);
            $role = \App\Role::where('name', substr($req->plan, 0, strpos($req->plan, "_")))->first();
            $user->role_id = $role->id;
            $user->initUsageByRole($role);//更改计划时切换资源
            $user->save();
            Log::info($user->name . " change plan to " . $req->plan);
        /* } catch(\Exception $e) { */
        /*     //get message from caught error */
        /*     $message = $e->getMessage(); */
        /*     //send back error message to view */
        /*     return redirect()->back()->withErrors(['message' => $message]); */
        /* } */
        return redirect('/app/profile?active=0');
    }

    /**
     * 获取帐单信息(JSON)
     */
    public function billings()
    {
        $fields = ["id", "billingPeriodStartDate", "billingPeriodEndDate", "currentBillingCycle", "planId", "price", "status"];
        $user = Auth::user();
        $res = [];
        foreach($user->subscriptions as $item) {
            $subscription = \Braintree\Subscription::find($item->braintree_id);
            $resitem = [];
            foreach($fields as $item2) {
                $resitem[$item2] = $subscription->$item2;
            }
            $resitem['invoice'] = $subscription->transactions[0]->id;//只要最新交易的发票
            array_push($res,  $resitem);
        }
        return $res;
    }
}
