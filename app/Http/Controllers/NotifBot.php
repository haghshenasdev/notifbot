<?php

namespace App\Http\Controllers;

use App\Models\ChatbotData;
use Illuminate\Http\Request;

class NotifBot extends Controller
{
    public array $configs = [
        'bale' => [
            'token' => '173614806:hheEZWmwBFVXcyEodH421lRaMiUikCCfTFeTLRZW',
            'apiUrl' => 'https://tapi.bale.ai/bot',
        ],
        'telegram' => [
            'token' => '774730190:AAFfnGzzPi00BQrVla_jTZYbQiiAS5oUG6E',
            'apiUrl' => 'https://api.telegram.org/bot',
        ]
    ];

    public string $state = '';

    public function bale(Request $request): void
    {
        $this->state = 'bale';
        $this->handelRequest($request);
    }

    public function telegram(Request $request): void
    {
        $this->state = 'telegram';
        $this->handelRequest($request);
    }

    public function handelRequest(Request $request): void
    {
        $update = $request->input();

        if (isset($update['message']['text'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];


            if ($text == '/start') {
                $this->sendMessage($chatId, "لطفاً کد دریافت اعلان خود را وارد کنید:");
            } elseif ($text == '/close') {
                if ($botData = ChatbotData::where($this->state . '_notifbot_id', $chatId)->first()) {
                    $botData->{$this->state . '_notifbot_id'} = null;
                    $botData->save();
                    $this->sendMessage($chatId, "سیستم اعلانات غیر فعال شد . \n برای راه اندازی مجدد کد دریافت شده از سامانه را وارد نمایید.");
                } else {
                    $this->sendMessage($chatId, 'اعلانات برای شما فعال نشده است.');
                }
            } else {
                if ($botData = ChatbotData::where('notifbot_code', $text)->first()) {
                    $botData->{$this->state . '_notifbot_id'} = $chatId;
                    $botData->notifbot_code = null;
                    $botData->save();
                    $this->sendMessage($chatId, "✅کد شما تایید شد.\n اطلاع رسانی‌ها از این به بعد برای شما از طریق همین ربات ارسال خواهد شد.\n\nبرای غیر فعال کردن دریافت اعلانات دستور close/ را ارسال نمایید.");
                } else {
                    $this->sendMessage($chatId, "❌ کد نامعتبر است. لطفاً دوباره تلاش کنید.");
                }
            }
        }
    }

    public function sendMessage($chatId, $text): void
    {
        $config = $this->configs[$this->state];
        $url = $config['apiUrl'] . $config['token'] . "/sendMessage?chat_id=$chatId&text=" . urlencode($text);
        file_get_contents($url);
    }

    public function sendMessageByUser($userId, $text): void
    {
        $chatId = ChatbotData::where('user_id', $userId)->first()->{$this->state . '_notifbot_id'};
        $this->sendMessage($chatId, $text);
    }
}
