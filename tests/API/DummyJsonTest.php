<?php

namespace Tests\API;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DummyJsonTest extends TestCase
{
    public function testThatDummyJsonReturnsManyUsers(): void
    {
        $client = new Client([
            'base_uri' => 'https://dummyjson.com',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $usersResponse = $client->get('users?limit=5');
        $content = $usersResponse->getBody()->getContents();
        $data = json_decode($content, true);

        $this->assertArrayHasKey('users', $data);
        $this->assertCount(5, $data['users']);
    }

    public function testThatDummyJsonReturnsSingleUser(): void
    {
        $client = new Client([
            'base_uri' => 'https://dummyjson.com',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $usersResponse = $client->get('users/1');
        $content = $usersResponse->getBody()->getContents();
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('firstName', $data);
        $this->assertArrayHasKey('lastName', $data);
        $this->assertArrayHasKey('email', $data);
    }
}
