<?php

namespace App\Http\Controllers;
use Braintree\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Log;
use DB;
use App\Role;
use App\Plan;
use App\Subscription;
use App\Webhook;
use App\Services\PaypalService;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * 显示支付表单
     */
    public function form(Request $req)
    {
        $planid = $req->plan;
        $plan = Plan::find($planid);
        return view('subscriptions.pay', ['plan'=>$plan]);
    }

    /**
     * 支付表单提示的处理
     * @warning 如果用户已经订阅了，不允许创建同样的订阅
     */
   public function pay(Request $req)
    {
        //check that we have nonce and plan in the incoming HTTP request
        if(empty( $req->input('planid') ) ){
            return redirect()->back()->withErrors(['message' => 'Invalid request']);
        }
        $plan = Plan::find(intval($req->input('planid')));
        $user = Auth::user();

        $service = new \App\Services\PaypalService;
        $approvalUrl = $service->createPayment($plan);
        //由于此时订阅的相关ID没产生，所以没办法通过保存ID，此时就先通过token作为中转
		$queryStr = parse_url($approvalUrl, PHP_URL_QUERY);
		$queryTmpArr = explode("&", $queryStr);
		$queryArr  = [];
		foreach($queryTmpArr as $key => $item) {
			$t = explode("=", $item);
			$queryArr[$t[0]] = $t[1];
        }

        //创建一个新的订阅(是否要清除已有的未支付订阅呢？需要的，防止数据库被填满，由于Paypal的token可能还在生效，所以在onPay时要确保不能支付成功)
        Subscription::where('user_id', $user->id)->where('quantity', 0)->delete();
        $subscription = new Subscription();
        $subscription->user_id = $user->id;
        $subscription->plan = $plan->name;
        $subscription->payment_id = $queryArr['token'];
        $subscription->quantity = 0;
        $subscription->save();

        return redirect($approvalUrl);
    }

    /**
     * 获取帐单信息(JSON)
     */
    public function billings()
    {
        $user = Auth::user();
        $res = [];
        $subscriptions = Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->get();
        $service = new PaypalService();
        $resItem = [];
        //没有返回交易记录，不知觉厉
        foreach($subscriptions as $item) {
            $transactions = $service->transactions($item->payment_id);
            if ($transactions == null)
                return [];
            foreach ($transactions as $t) {
                $amount = $t->getAmount();
                if ($amount == null)
                    continue;
                $carbon = new Carbon($t->getTimeStamp(), $t->getTimeZone());
                $carbon->tz = Carbon::now()->tz;
                
                $resItem["id"] = $t->getTransactionId();
                $resItem["plan"] = $item->plan;
                $resItem["amount"] =  $amount->getValue();
                $resItem["currency"] = $amount->getCurrency();
                $resItem["type"] = $t->getTransactionType();
                $resItem["startDate"] = $carbon->toDateTimeString();
                $resItem["endDate"] = strpos($item->plan, "monthly") > 0 ? $carbon->addMonth()->toDateTimeString() : $carbon->addYear()->toDateTimeString();
                $resItem["status"] = $t->getStatus();
                array_push($res, $resItem);   
            }
        }
        return $res;
    }

    /**
     * 获取所有计划
     */
    public function plans()
    {
        $items = Role::with('permissions', 'policies')->where('plan', '<>', null)->get();
        foreach ($items as $key=>$item) {
            $item->groupPermissions = $item->permissions->groupBy('table_name');
            $item->append('plans');
        }
        return $items;
    }

    /**
     * 显示所有服务端计划(主要用于测试目的)
     */
    public function showPlans()
    {
        $service = new PaypalService();
        return $service->dropPlans();
    }

    /**
     * 客户同意支付后的回调
     */
    public function onPay(Request $request)
    {
        echo "processing...don't close the window";
        $subscription = Subscription::where('payment_id', $request->token)->first();
        if (!($subscription instanceof Subscription)) {
            abort(401, "no subscription found");
        }

        $service = new PaypalService();

        $payment = $service->onPay($request);
        //中途取消的情况下返回profile页面
        if ($payment == null)return redirect('/app/profile?active=0');
            //abort(401, "error on payment");

        $subscription->payment_id = $payment->getId();
        $subscription->quantity = 1;
        $subscription->save();
        $user = $subscription->user;
        //原来有订阅的情况下，应该取消订阅,这步可以推到队列中去做
        if ($user->subscription_id > 0) {
            $service->suspendSubscription($user->subscription->payment_id);
        }
        $plan = Plan::where('name', $subscription->plan)->first();
        $role = $plan->role;
        $user->subscription_id = $subscription->id;
        $user->role_id = $role->id;
        $user->initUsageByRole($role);//更改计划时切换资源
        $user->save();
        Log::info($user->name . " change plan to " . $plan->name);

        return redirect('/app/profile?active=0');
    }

    /**
     * 处理支付的一些通知
     */
    public function onPayWebhooks(Request $request)
    {
        Log::info('webhooks id: '.$request->id);
        $webhook_id = $request->id;//webhook id
        $select = Webhook::where('webhook_id',$webhook_id)->get();
        Log::info('$select: '.$select);
        Log::info('count($select): '.count($select));
        //$select = DB::select('select * from webhooks where webhook_id = :webhook_id',['webhook_id'=>$webhook_id]);
        if(count($select)==0){
            $webhook = new Webhook;
            $webhook->webhook_id = $webhook_id;
            $webhook->create_time = $request->create_time;
            $webhook->resource_type = $request->resource_type;
            $webhook->event_type = $request->event_type;
            $webhook->summary = $request->summary;
            $webhook->webhook_content = serialize($request->resource);
            try {
                $re = $webhook->save();
                Log::info('$webhook->save(): '.$re);
            } catch(\Exception $e) {
                Log::error("save webhooks failed:" . $e->getMessage());
                return null;
            }
        }
    }
}
