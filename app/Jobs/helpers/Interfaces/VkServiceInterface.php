<?php

namespace App\Jobs\helpers\Interfaces;


interface VkServiceInterface
{
    public function getVkPosts(): array;
    public function setCount(int $count): void;
    public function setOffset(int $offset): void;
}