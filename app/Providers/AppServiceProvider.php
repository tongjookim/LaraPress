<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $this->applySmtpPlugin();
    }

    private function applySmtpPlugin(): void
    {
        try {
            $active = json_decode(Setting::get('active_plugins', '[]'), true);
            if (!is_array($active) || !in_array('smtp-mailer', $active)) {
                return;
            }

            $json     = Setting::get('plugin_smtp-mailer_settings', '{}');
            $settings = json_decode($json, true);
            if (!is_array($settings) || empty($settings['smtp_host'])) {
                return;
            }

            config([
                'mail.default'                    => 'smtp',
                'mail.mailers.smtp.host'          => $settings['smtp_host'],
                'mail.mailers.smtp.port'          => (int) ($settings['smtp_port'] ?? 587),
                'mail.mailers.smtp.encryption'    => $settings['smtp_encryption'] ?? 'tls',
                'mail.mailers.smtp.username'      => $settings['smtp_username'] ?? null,
                'mail.mailers.smtp.password'      => $settings['smtp_password'] ?? null,
                'mail.from.address'               => $settings['smtp_from_address'] ?? config('mail.from.address'),
                'mail.from.name'                  => $settings['smtp_from_name']    ?? config('mail.from.name'),
            ]);
        } catch (\Throwable) {
            // DB 미설치 등 초기 상태에서는 조용히 무시
        }
    }
}
