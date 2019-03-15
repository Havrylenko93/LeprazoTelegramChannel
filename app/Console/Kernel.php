<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule,
    Illuminate\Foundation\Console\Kernel as ConsoleKernel,
    Illuminate\Contracts\Events\Dispatcher,
    Illuminate\Contracts\Foundation\Application;

class Kernel extends ConsoleKernel
{
    protected $httpClient;

    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
        $this->httpClient = new \GuzzleHttp\Client();
    }

    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        /**
         * !!! what i need?
         *
         * 1) just photo / photos
         * 2) just test
         * 3) photo/photos and text
         *
         *  Need to foreach all attachments and add biggest from each of them
         *
         */

        $schedule->call(function () {
            $baseUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_ID');
            $method = '/sendPhoto';

            $message = http_build_query([
                'chat_id' => '@marvel_dc_marvel_dc',
                'photo' => 'https://sun6-2.userapi.com/c855332/v855332840/40d3/f4HAGHi_t1M.jpg',
                'caption' => 'фото с текстом, ну нихуя себе'
            ]);
            $response = $this->httpClient->request('POST', $baseUrl . $method . '?' . $message)->getBody()->getContents();
        })->everyThirtyMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
