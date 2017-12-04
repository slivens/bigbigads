<?php
namespace App\Contracts;

use Illuminate\Mail\Mailable;

interface UserService
{
    /**
     * 返回当前使用的mail驱动
     * @return string mailgun,log,smtp...
     */
    public function mailDriver() : string;

    /**
     * 发送邮件服务
     * @param string $to
     * @param string $mail
     * @param array $options
     * @return mixed
     */
    public function sendMail(string $to, Mailable $mail, array $options = []);
}
