<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        $siteName = \App\Models\Setting::get('site_name', 'Laraboard');

        return (new MailMessage)
            ->subject("[{$siteName}] 이메일 인증을 완료해주세요")
            ->greeting('안녕하세요!')
            ->line('아래 버튼을 클릭하여 이메일 인증을 완료해주세요.')
            ->action('이메일 인증하기', $url)
            ->line('이 링크는 60분간 유효합니다.')
            ->line('본인이 가입하지 않았다면 이 메일을 무시하셔도 됩니다.')
            ->salutation("감사합니다.\n{$siteName} 팀");
    }
}
