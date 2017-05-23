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
                $users = User::all();
                foreach ($users as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            } else if ($item == '#1') {
                $users = User::where('state', 1)->get();
                foreach ($users as $user) {
                    if (array_key_exists($user->email, $maillist)) 
                        continue;
                    $maillist[$user->email] = true;
                }
            } else if ($item == '#2') {
                $users = User::where('state', 0)->get();
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
        foreach (array_keys($maillist) as $to) {
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
}
