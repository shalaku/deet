<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $htmlContent = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Forgot Password</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    border-radius: 8px;
                }
                h2 {
                    color: #333;
                }
                p {
                    font-size: 16px;
                    line-height: 1.5;
                    color: #555;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>パスワード再設定</h2>
                <p>パスワードの再設定を受け付けました。<br> 下記認証コードを使用してパスワードのリセットを行ってください。</p>
                <p><strong>' . $this->token . '</strong></p>
                <p>If you didn\'t request a password reset, please ignore this email.</p>
            </div>
        </body>
        </html>
        ';

        return $this->subject('パスワード再設定用の認証コード - Deet')
            ->html($htmlContent);
    }
}
