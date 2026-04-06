<?php

namespace Tests\Services;

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use SamMakesCode\DummyJSON\Exceptions\ModelNotFoundException;
use SamMakesCode\DummyJSON\Models\User;
use SamMakesCode\DummyJSON\Services\UsersService;

class UsersServiceTest extends TestCase
{
    public function testCanGetByIdOptimalCase(): void
    {
        $faker = Factory::create();

        $testUserId = rand(1, 100000);
        $testForename = $faker->firstName;
        $testLastname = $faker->lastName;
        $testEmail = $faker->email;
        $response = new Response(
            200,
            [],
            json_encode([
                'id' => $testUserId,
                'firstName' => $testForename,
                'lastName' => $testLastname,
                'email' => $testEmail,
            ]),
        );
        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('get')
            ->once()
            ->andReturn($response);

        $usersService = new UsersService($clientMock);
        $user = $usersService->getById(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($testUserId, $user->id);
        $this->assertEquals($testForename, $user->firstName);
        $this->assertEquals($testLastname, $user->lastName);
        $this->assertEquals($testEmail, $user->email);
    }

    public function testCanGetByIdHandlesBadId(): void
    {
        $clientMock = Mockery::mock(Client::class);
        $usersService = new UsersService($clientMock);
        $this->expectException(InvalidArgumentException::class);
        $usersService->getById(0);
    }

    public function testCanGetByIdThrowsException(): void
    {
        $response = new Response(400);
        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('get')
            ->once()
            ->andReturn($response);

        $this->expectException(ModelNotFoundException::class);

        $usersService = new UsersService($clientMock);
        $usersService->getById(1);
    }

    public function testCanGetPageOptimalCase(): void
    {
        $faker = Factory::create();
        $users = [];
        for ($i = 0; $i < 25; $i++) {
            $users[] = [
                'id' => $i + 1,
                'firstName' => $faker->firstName,
                'lastName' => $faker->lastName,
                'email' => $faker->email,
            ];
        }
        $response = new Response(200, [], json_encode(['users' => $users]));
        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('get')
            ->once()
            ->andReturn($response);

        $usersService = new UsersService($clientMock);
        $users = $usersService->getPage();

        $this->assertIsArray($users);
        $this->assertCount(25, $users);
    }

    public function testCanCreate()
    {
        $faker = Factory::create();
        $testId = rand(1, 1000000000);
        $response = new Response(200, [], json_encode([
            'id' => $testId,
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => $faker->email,
        ]));
        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('post')
            ->once()
            ->andReturn($response);
        $usersService = new UsersService($clientMock);
        $response = $usersService->create('John', 'Smith', 'john@example.org');

        $this->assertIsInt($response);
        $this->assertEquals($testId, $response);
    }
}
