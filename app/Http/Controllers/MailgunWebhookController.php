<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendRegistMail;
use Log;

class MailgunWebhookController extends Controller
{
    public function sendRegisterVerify(&$request)
    {
        $user = \App\User::where("email", $request->recipient)->first();    
        if (!($user instanceof \App\User)) {
            Log::warning("registered email not found??");
            return;
        }
        dispatch(new SendRegistMail($user, true)); 
        /* dispatch(new LogAction("USER_SENDAGAIN", json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip())); */
    }

    /**
     * 对于拒收邮件使用gmail重发一次，目前与onDropped的处理逻辑完全一样
     */
    public function onBounced(&$request)
    {
        $this->onDropped($request);
    }

    /**
     * 对于被扔掉的邮件使用其他邮箱重发
     * （比如用户点了拒收或者其他原因会导致被扔掉)
     */
    public function onDropped(&$request)
    {
        Log::info("mailgun failed:" . json_encode($request->all()));
        switch($request->input('X-Mailgun-Tag')) {
            case 'registerVerify':
                $this->sendRegisterVerify($request);
                break;
        }
    }

    /**
     * 目前只处理触发式邮件
     * @warning 如果对营销式邮件使用SMTP重发，那么SMTP将很快不正常。
     * 对于营销式邮件，只能逐步提高其成功率，而不应该改用SMTP
     */
    public function onWebhook(Request $request)
    {
        /* Log::debug("webhook:" . json_encode($request->all())); */
        $key = config('services.mailgun.secret');
        $sign = hash_hmac("sha256", $request->timestamp . $request->token, $key);
        if ($sign != $request->signature) {
            Log::info("sign is not the same");
            return;
        }
        if ($request->event === "bounced" || $request->event === "failed") {
            $this->onBounced($request);
        } else if ($request->event === "dropped") {
            $this->onDropped($request);
        }
    }
}
