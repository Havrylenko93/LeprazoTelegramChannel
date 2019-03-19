<?php

namespace App\Jobs\helpers;

use GuzzleHttp\Client,
    App\Jobs\helpers\Interfaces\TelegramServiceInterface;

class TelegramService implements TelegramServiceInterface
{
    /** @var Client */
    private $httpClient;

    /**
     * TelegramService constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @param string $method ('/sendMediaGroup', '/sendPhoto', '/sendMessage')
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendToChannel(string $method, array $params): void
    {
        $baseUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_ID');

        $message = http_build_query($params);

        $this->httpClient->request('POST', $baseUrl . $method . '?' . $message);
    }

    /**
     * @param \stdClass $post
     * @return array
     */
    public function prepareData(\stdClass $post): array
    {
        if (!empty($post->attachments) && reset($post->attachments)->type !== 'photo') {
            return [null, null];
        }

        $method = '/sendMessage';
        $params = [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $post->text
        ];

        if (!empty($post->attachments) && count($post->attachments) === 1) {
            $method = '/sendPhoto';
            $params = [
                'chat_id' => env('TELEGRAM_CHAT_ID'),
                'photo' => $this->getBiggestPhoto(reset($post->attachments)),
                'caption' => $post->text
            ];
        } elseif (!empty($post->attachments) && count($post->attachments) > 1) {
            $method = '/sendMediaGroup';
            $arrayOfPhotos = [];

            foreach ($post->attachments as $item) {
                $arrayOfPhotos[] = [
                    'type' => 'photo',
                    'media' => $this->getBiggestPhoto($item),
                    'caption' => $post->text
                ];
            }

            $params = [
                'chat_id' => env('TELEGRAM_CHAT_ID'),
                'media' => json_encode($arrayOfPhotos)
            ];
        }

        return [$method, $params];
    }

    private function getBiggestPhoto(\stdClass $attachment): string
    {
        $inputArrayOfAttachments = (array)$attachment->photo;

        $onlyLinks = array_filter($inputArrayOfAttachments, function ($key) {
            return preg_match('/photo_(.)/', $key) === 1;
        }, ARRAY_FILTER_USE_KEY);

        return end($onlyLinks);
    }

}