<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Cache;
use Carbon\Carbon;

class VerifyCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $viewName = 'emails.verify_mail';
    public $subject = "Bigbigads:Please Verify Your email";
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
        // 不新增字段，改为存入缓存中，过期时间暂定为10分钟
        $verifyCode = str_random(40);
        $expiresAt = Carbon::now()->addMinutes(10);
        $userCode = 'verifyCode_'.$user->id;
        // 重新发起发送验证邮箱邮件,上一次的验证码即时未过期也强制删除,重新创建
        if (Cache::get($userCode)) {
            Cache::forget($userCode);
        }
        Cache::put($userCode, $verifyCode, $expiresAt);
        return ['name' => $user->name,
                'link' => "{$host}subEmailVerify?token={$verifyCode}&subEmail={$user->subscription_email}"
               ];
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->viewName)
            ->with($this->params())->subject($this->subject);
    }
}
