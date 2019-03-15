<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule,
    Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $baseUrl = 'https://api.telegram.org/bot' . '752332983:AAEQUuKF1T-UwsFKdtYe0Kw2iVcIVP1ztc8';
            $httpClient = new \GuzzleHttp\Client();
            $method = '/sendPhoto';

            $message = http_build_query([
                'chat_id' => '@marvel_dc_marvel_dc',
                'photo' => 'https://sun6-2.userapi.com/c855032/v855032396/1a93/zjJIFJ3CB58.jpg',
                'caption' => 'фото с текстом, ну нихуя себе'
            ]);
            $response = $httpClient->request('POST', $baseUrl . $method . '?' . $message)->getBody()->getContents();
        })->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
