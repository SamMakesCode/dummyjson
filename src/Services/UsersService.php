<?php

namespace SamMakesCode\DummyJSON\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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

        try {
            $response = $this->client->get('users/' . $id);
        } catch (ClientException $clientException) {
            if ($clientException->getResponse()->getStatusCode() === 404) {
                throw new ModelNotFoundException('No user with that ID exists.', 0, $clientException);
            }

            throw $clientException;
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
            'json' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
            ],
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
