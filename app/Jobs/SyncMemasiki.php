<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable,
    Illuminate\Contracts\Queue\ShouldQueue,
    GuzzleHttp\Client;

class SyncMemasiki implements ShouldQueue
{
    use Queueable;

    protected $httpClient;

    public function handle()
    {
        $this->setHttpClient();

        /**
         * !!! what i need?
         *
         * 1) just photo / photos
         * 2) just text
         * 3) photo/photos and text
         *
         *  Need to foreach all attachments and add biggest from each of them
         *
         */
        $baseUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_ID');


        /**
         * need to check
         * if post has one attachment ->
         *          then we need to use SendMessage method
         * elseif post has more than one attachment ->
         *          we need to use  sendMediaGroup method and map "text" filed to all of "caption fields"
         */

        $method = '/sendMediaGroup';

        $message = http_build_query([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'media' => json_encode([
                [
                    'type' => 'photo',
                    'media' => 'https://sun6-5.userapi.com/c855032/v855032396/1a96/DLX742xzfGw.jpg',
                    'caption' => 'same text'
                ],
                [
                    'type' => 'photo',
                    'media' => 'https://sun6-1.userapi.com/c851432/v851432003/dc40f/1XlLOPRqqJM.jpg',
                    'caption' => 'same text'
                ],
                [
                    'type' => 'photo',
                    'media' => 'https://sun6-3.userapi.com/c848416/v848416887/14e1fd/eq-2ECGx-PE.jpg',
                    'caption' => 'same text'
                ]
            ])
        ]);

        $this->httpClient->request('POST', $baseUrl . $method . '?' . $message)->getBody()->getContents();
    }

    private function setHttpClient()
    {
        $this->httpClient = new Client();
    }
}
