<?php

namespace SamMakesCode\DummyUsers\Services;

use GuzzleHttp\Client;

class UsersService
{
    public function __construct(
        private Client $client,
    ) {}
}
