<?php

namespace App\Service;

class TelegramBotHandler
{
    public $bot_token;
    public $channel;
    public $chat_bot_url;

    public function __construct($bot_token, $channel)
    {
        $this->bot_token = env("TELEGRAM_BOT_TOKEN");
        $this->channel = env("TELEGRAM_CHANNEL");
        $this->chat_bot_url = env("TELEGRAM_CHAT_BOT_URL");
    }

    public function sendMessage($message)
    {
        $url_send = $this->chat_bot_url . $this->bot_token . '/sendMessage';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url_send,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 360,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'chat_id=' . $this->channel . '&text=' . $message,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }





}
