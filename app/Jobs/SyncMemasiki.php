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
        $vkPosts = array_reverse($this->vkService->getVkPosts());

        foreach ($vkPosts as $post) {
            if ($this->checkPostCreationDate($post) === true || $this->checkIsPinned($post) === true) {
                continue;
            }

            list($method, $params) = $this->telegramService->prepareData($post);

            if (empty($method) || empty($params)) {
                continue;
            }

            $this->telegramService->sendToChannel($method, $params);
            sleep(10);
            file_put_contents(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople', $post->date);
        }
    }

    private function checkPostCreationDate(\stdClass $post): bool
    {
        $postIsOlder = false;

        if (file_exists(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople')) {
            if ($post->date <= file_get_contents(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople')) {
                $postIsOlder = true;
            }
        } else {
            file_put_contents(storage_path() . DIRECTORY_SEPARATOR . 'databaseForPoorPeople', '1552767083');
        }

        return $postIsOlder;
    }

    private function checkIsPinned(\stdClass $post): bool
    {
        $isPinned = false;

        if (!empty($post->is_pinned)) {
            $isPinned = true;
        }

        return $isPinned;
    }

    private function init()
    {
        $this->httpClient = new Client();
        $this->vkService = new VkService();
        $this->telegramService = new TelegramService();
    }
}
