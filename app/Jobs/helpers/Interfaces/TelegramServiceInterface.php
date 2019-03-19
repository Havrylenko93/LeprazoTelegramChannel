<?php

namespace App\Jobs\helpers\Interfaces;

interface TelegramServiceInterface
{
    public function sendToChannel(string $method, array $params): void;
}