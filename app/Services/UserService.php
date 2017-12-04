<?php
namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Mail;
use Mailgun;
use Log;

class UserService implements \App\Contracts\UserService
{
    private $options;
    public function __construct(array $options)
    {
        $this->options = new Collection($options);
    }

    /**
     * @{inheritDoc}
     */
    public function mailDriver() : string
    {
        if ($this->options['useMailgun'])
            return 'mailgun';
        return config('mail.driver');
    }

    /**
     * @{inheritDoc}
     */
    public function sendMail(string $to, Mailable $mail, array $options = []) 
    {
        $useMailgun = $this->options['useMailgun'];
        $forceDefault = isset($options['forceDefault']) ? $options['forceDefault'] : false;
        if ($useMailgun && !$forceDefault) {
            // Mailgun不支持直接发送Maiable接口，需要自己build
            $mail->build();
            $res = Mailgun::send($mail->view, $mail->viewData, function($message) use($to, $options, $mail) {
                $message->to($to)->subject($mail->subject);
                if (isset($options['tags']))
                    $message->tag($options['tags']);
            });
            /* Log::debug("res:" . $res->status , ['res' => $res]); */
            return $res;
        } else {
            // 使用系统默认邮箱服务
            Mail::to($to)->send($mail);//发送验证邮件
        }
    }
}

