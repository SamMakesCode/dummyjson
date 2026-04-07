<?php

namespace SamMakesCode\DummyJSON;

use GuzzleHttp\Client;
use SamMakesCode\DummyJSON\Services\UsersService;

class DummyJSON
{
    private Client $client;

    public function __construct(

    ) {
        $this->createClient();
    }

    private function createClient(): void
    {
        $this->client = new Client([
            'base_uri' => 'https://dummyjson.com',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function users(): UsersService
    {
        return new UsersService($this->client);
    }
}
