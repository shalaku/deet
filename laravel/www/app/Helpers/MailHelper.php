<?php
// app/Helpers/MailHelper.php
namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper
{
    /**
     * メールを送信するヘルパー関数
     *
     * @param string $to 受信者のメールアドレス
     * @param string $subject メールの件名
     * @param string $body メールの本文
     * @return void
     */
    public static function sendEmail($to, $subject, $body)
    {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                    ->subject($subject);
        });
    }
}