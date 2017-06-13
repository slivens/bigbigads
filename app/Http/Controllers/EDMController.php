<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Maillist;
use Mailgun;
use Carbon\Carbon;
use Mail;

class EDMController extends Controller
{
    /**
     * 显示营销邮件首页
     */
    public function index()
    {
        $mails = config('mail.edm');
        $categories = Maillist::groupBy('category')->get();
        //'#'开头的是有特殊含义的
        $mailTypes = ['#0' => '所有注册用户', 
            '#1' => '已通过审核用户',
            '#2' => '未通过审核用户'];
        foreach ($categories as $item) {
            $mailTypes[$item->category] = $item->category;
        }
        
        return view('edm.index')->with('mailTypes', $mailTypes)->with("mails", $mails);
    }

    /**
     * 群发邮件
     */
    public function send(Request $request)
    {
        $mails = config('mail.edm');
        $mailConfig = $mails[$request->mail];
        $mailType = $request->mailType;
        $interval = intval($request->interval);
        $warmTime = 3;
        $useMailgun = false;
        $maillist = [];
        foreach ($mailType as $item) {
            if ($item == '#0') {
                $users = User::where('edm', 1)->get();
                foreach ($users as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            } else if ($item == '#1') {
                $users = User::where('edm', 1)->where('state', 1)->get();
                foreach ($users as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            } else if ($item == '#2') {
                $users = User::where('edm', 1)->where('state', 0)->get();
                foreach ($users as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            } else {
                $grouped = Maillist::where('category', $item)->get();
                foreach ($grouped as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            }
        }


        /* $to = 'm13799329269@163.com'; */
        if ($interval < 10) //间隔必须至少10秒
            $interval = 10;
        if (!empty(env('MAILGUN_USERNAME'))) {
            $useMailgun = true;
        }

        $host = config('app.url');
        foreach (array_keys($maillist) as $to) {

            //只要点击取消订阅，mailgun都会记录，以后营销邮件都不会发出去;
            //取消订阅是按照tag分类的，也就是说每周精选不要了；其他营销类邮件还是能发得出去；
            //但是如果是本站用户，则edm设置为0后，就再也不会向其发送所有的营销类邮件了。
            /* $user = User::where('email', $to)->first(); */
            /* $token = ""; */
            /* if ($user) */
            /*     $token = $user->verify_token; */
            /* $unsubscribeUrl = $host . "edm/unsubscribe?email={$to}&token={$token}" ; */
            $mail = new $mailConfig['class']($to);
            $mail->build();
            if ($useMailgun) {
                Mailgun::later($warmTime, $mail->view, $mail->buildViewData(), function($message) use($to, $mailConfig, $request) {
                    $message->to($to)
                        ->subject($mailConfig['name'])
                        ->trackClicks(true)
                        ->trackOpens(true)
                        ->tag(['edm', $request->mail]);
                });
            } else {
                Mail::to($to)->later(Carbon::now()->addSeconds($warmTime), $mail);//发送验证邮件
            }
            $warmTime += $interval;
        }
        return back()->with("sent", array_keys($maillist));
    }

    /**
     * 自定义的取消订阅
     * @var $request->email 邮件
     * @var $request->token token(防止恶意批量取消订阅)
     * @remark 目前使用的mailgun自带的取消订阅，在mailgun的webhook中处理，此处实际并没用上
     */
    public function unsubscribe(Request $request)
    {
        if (!$request->has('email') )
            return $this->messageRaw('wrong params');
        $token = $request->has('token') ? $request->token : '';
        $user = User::where(['email' => $request->email])->first();
        //如果不是本系统的用户，统一提示取消订阅成功（mailgun会阻止往取消订阅的用户发送邮件)
        if (!$user) {
            return $this->messageRaw("{$request->email} unsubscribe successfully", 'success');
        }

        if ($user && $user->verify_token != $token) {
            return $this->messageRaw("{$request->email}:invalid params");
        }
        if (!$user->edm) {
            return $this->messageRaw("{$request->email} is not on subscribtion");
        }
        $user->edm = 0;
        $user->save();
        //更好的做法是先提示是否确定取消订阅，确定后再取消
        return $this->messageRaw("{$request->email} unsubscribe successfully", 'success');
    }
}
