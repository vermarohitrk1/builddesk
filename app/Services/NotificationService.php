<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification through multiple channels.
     */
    public function notify(User $user, string $message, array $channels = ['in-app'])
    {
        foreach ($channels as $channel) {
            $this->sendToChannel($user, $message, $channel);
        }
    }

    protected function sendToChannel(User $user, string $message, string $channel)
    {
        switch ($channel) {
            case 'email':
                // Mail::to($user->email)->send(...);
                break;
            case 'sms':
                // SMSProxy::send($user->phone, $message);
                break;
            case 'whatsapp':
                // WhatsAppAPI::send($user->phone, $message);
                break;
            case 'in-app':
                // $user->notifications()->create([...]);
                break;
            default:
                Log::warning("Notification channel {$channel} not supported.");
        }
    }

    /**
     * Future WhatsApp logic for specific updates.
     */
    public function sendWhatsAppUpdate(string $phone, string $template, array $data)
    {
        // Integration with Meta WhatsApp Cloud API or third party
        Log::info("WhatsApp Update to {$phone} with template {$template}");
    }
}
