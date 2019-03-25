<?php

namespace App\Jobs\helpers;

use GuzzleHttp\Client,
    App\Jobs\helpers\Interfaces\VkServiceInterface;

class VkService implements VkServiceInterface
{
    /** @var Client */
    private $httpClient;
    /** @var int */
    private $count = 100;
    /** @var int */
    private $offset = 0;
    /**
     * @var array
     */
    public static $arrayOfVkGroups = [
        '-65960786',
        '-12382740',
        '-69743495'
    ];

    /**
     * VkService constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVkPosts(): array
    {
        $baseUrl = 'https://api.vk.com/method/wall.get';
        $content = [];

        foreach (self::$arrayOfVkGroups as $groupId) {
            $groupPosts = (array)json_decode($this->httpClient->request('GET', $baseUrl . '?' . http_build_query([
                    'owner_id' => $groupId,
                    'offset' => $this->offset,
                    'count' => $this->count,
                    'v' => env('VK_API_VERSION'),
                    'access_token' => env('VK_ACCESS_TOKEN'),
                ]))->getBody()->getContents())->response->items;
            $content = array_merge($content, $groupPosts);
        }

        return $content;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }
}