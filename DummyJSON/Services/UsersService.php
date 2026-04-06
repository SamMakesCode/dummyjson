<?php

namespace SamMakesCode\DummyJSON\Services;

use GuzzleHttp\Client;
use InvalidArgumentException;
use SamMakesCode\DummyJSON\Exceptions\ModelNotFoundException;
use SamMakesCode\DummyJSON\Models\User;

class UsersService
{
    public function __construct(
        private Client $client,
    ) {}

    public function getById(int $id): User
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        $response = $this->client->get('users/' . $id);

        if ($response->getStatusCode() === 400) {
            throw new ModelNotFoundException('No user with that ID exists.');
        }

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return $this->hydrateModelWithData($data);
    }

    public function getPage(int $page = 1, int $perPage = 25): array
    {
        $limit = $perPage;
        $skip = ($page - 1) * $limit;

        $uriTemplate = 'users?limit=%s&skip=%s';
        $uri = sprintf($uriTemplate, $limit, $skip);

        $response = $this->client->get($uri);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        $users = array_map(function ($user) {
            return $this->hydrateModelWithData($user);
        }, $data['users']);
        return $users;
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
    ): int {
        $response = $this->client->post('users/add', [
            'json' => json_encode([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
            ]),
        ]);

        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);
        return $this->hydrateModelWithData($data)->id;
    }

    private function hydrateModelWithData(array $data): User
    {
        return new User(
            $data['id'],
            $data['firstName'],
            $data['lastName'],
            $data['email'],
        );
    }
}
