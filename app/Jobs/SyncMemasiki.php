<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable,
    Illuminate\Contracts\Queue\ShouldQueue,
    GuzzleHttp\Client,
    App\Jobs\helpers\VkService,
    App\Jobs\helpers\TelegramService;

class SyncMemasiki implements ShouldQueue
{
    use Queueable;
    /** @var Client */
    protected $httpClient;
    /** @var VkService */
    protected $vkService;
    /** @var TelegramService */
    protected $telegramService;

    public function handle()
    {
        $this->init();
        /**
         * !!! what i need?
         *
         * 1) just photo / photos
         * 2) just text
         * 3) photo/photos and text
         *
         *  Need to foreach all attachments and add biggest from each of them
         *
         *
         * need to check
         * if post has one attachment ->
         *          then we need to use SendMessage method
         * elseif post has more than one attachment ->
         *          we need to use  sendMediaGroup method and map "text" filed to all of "caption fields"
         */
        $vkPosts = array_reverse($this->vkService->getVkPosts());

        foreach ($vkPosts as $post) {
            if (file_exists(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople')) {
                if ($post->date <= file_get_contents(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople')) {
                    continue;
                }
            }

            list($method, $params) = $this->telegramService->prepareData($post);

            if (empty($method) || empty($params)) {
                continue;
            }

            $this->telegramService->sendToChannel($method, $params);
            break; // todo: need to delete this
        }
    }

    private function init()
    {
        $this->httpClient = new Client();
        $this->vkService = new VkService();
        $this->telegramService = new TelegramService();
    }
}
