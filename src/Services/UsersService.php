<?php

namespace SamMakesCode\DummyJSON\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use SamMakesCode\DummyJSON\Exceptions\ModelNotFoundException;
use SamMakesCode\DummyJSON\Models\User;

readonly class UsersService
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @return array<string, mixed>
     */
    private function extractSingle(string $bodyContents): array
    {
        $data = (array)json_decode($bodyContents, true);

        return [
            'id' => $data['id'],
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
        ];
    }

    /**
     * Retrieve a single user using an ID
     */
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

        return $this->hydrateModelWithData(
            $this->extractSingle(
                $response
                    ->getBody()
                    ->getContents(),
            ),
        );
    }

    /**
     * Return a page of users
     * @return array<User>
     * @throws GuzzleException
     */
    public function getPage(int $page = 1, int $perPage = 25): array
    {
        $uriTemplate = 'users?limit=%s&skip=%s';

        $limit = $perPage;
        $skip = ($page - 1) * $limit;
        $uri = sprintf($uriTemplate, $limit, $skip);

        $response = $this->client->get($uri);
        $body = $response->getBody()->getContents();
        $data = (array)json_decode($body, true);
        $users = [];
        foreach ($data['users'] as $datum) {
            $users[] = $this->hydrateModelWithData($datum);
        }
        return $users;
    }

    /**
     * Create a user
     *
     * @throws GuzzleException
     */
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
        $data = (array)json_decode($contents, true);
        return $this->hydrateModelWithData($data)->id;
    }

    /**
     * @param array<string, mixed> $data
     */
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
