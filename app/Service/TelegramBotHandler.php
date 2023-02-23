<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TelegramBotHandler
{
    private $bot_token;
    private $channel;
    private $api_url;
    private $send_url;

    public function __construct()
    {
        $this->bot_token = env("TELEGRAM_BOT_TOKEN");
        $this->channel = env("TELEGRAM_CHANNEL");
        $this->api_url = env("TELEGRAM_CHAT_BOT_URL");
        $this->send_url = $this->api_url . $this->bot_token . '/sendMessage?chat_id=' . $this->channel . "&text=";
    }

    /** Send message necessary in the function
     * @param $message
     * @return mixed
     */
    public function sendMessage($message)
    {
        $result = $this->sendApi($message);
        return json_decode($result->body());
    }

    /** Send message error to channel telegram
     * @param $server
     * @param $api
     * @param $error
     * @param $paramInput
     * @return mixed
     */
    public function sendErrorLog($server, $api, $error, $paramInput = "")
    {
        $message = 'Thời gian: ' . Carbon::now() . "\n" .
            'Server: ' . $server .  "\n" .
            'Api: ' . $api . "\n" .
            'Lỗi: ' . $error . "\n" .
            'Data: ' . $paramInput . "\n";
        $result = $this->sendApi($message);
        return json_decode($result->body());
    }

    /** Send message to Api chat bot telegram
     * @param $message
     * @return \Illuminate\Http\Client\Response
     */
    public function sendApi($message)
    {
        $result = Http::get($this->send_url . $message . "&parse_mode=HTML");
        return $result;
    }





}
