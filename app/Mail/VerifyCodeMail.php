<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Cache;
use Carbon\Carbon;
use App\Jobs\LogAction;
use App\ActionLog;

class VerifyCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function params()
    {
        $user = $this->user;
        $host = config('app.url');
        // 不新增字段，改为存入缓存中，过期时间改为2天
        $verifyCode = str_random(40);
        $expiresAt = Carbon::now()->addDays(2);
        $userCode = 'verifyCode_'.$user->id;
        // 重新发起发送验证邮箱邮件,上一次的验证码即时未过期也强制删除,重新创建
        if (Cache::get($userCode)) {
            Cache::forget($userCode);
        }
        Cache::put($userCode, $verifyCode, $expiresAt);
        // 由于采用缓存无法采用其他方式获知激活链接，存入action_log，当用户接收不到邮件时用于补救激活邮件
        $link = "{$host}subscription_email/verify?token={$verifyCode}&subEmail={$user->subscription_email}";
        dispatch(new LogAction(ActionLog::ACTION_EMAIL_EFFECTIVE_URL, $link, '', $user->id, ''));
        return ['name' => $user->name,
                'link' => $link
               ];
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verify_mail')
            ->with($this->params())->subject('Bigbigads:Please Verify Your email');
    }
}
