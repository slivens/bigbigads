<?php

namespace App\Http\Controllers;
use Braintree\ClientToken;
use Braintree\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    //
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

   public function pay(Request $req)
    {
        //check that we have nonce and plan in the incoming HTTP request
        if( empty($req->input( 'payment-method-nonce' ) ) || empty( $req->input('plan') ) ){
            return redirect()->back()->withErrors(['message' => 'Invalid request']);
        }
        $user = Auth::user();
        try {
            $subscription = $user->newSubscription('main', $req->plan )->create( Input::get( 'payment-method-nonce' ), [
                'email' => $user->email
            ]);
        } catch(\Exception $e) {
            //get message from caught error
            $message = $e->getMessage();
            //send back error message to view
            return redirect()->back()->withErrors(['message' => $message]);
        }
        return redirect('/app/profile?active=0');
    }

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
