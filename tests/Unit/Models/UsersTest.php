<?php

namespace Tests\Unit\Models;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use SamMakesCode\DummyJSON\Models\User;

class UsersTest extends TestCase
{
    public function testThatModelCanSerializeCorrectly(): void
    {
        $faker = Factory::create();

        $testId = $faker->numberBetween();
        $testFirstName = $faker->firstName;
        $testLastName = $faker->lastName;
        $testEmail = $faker->email;

        $user = new User(
            $testId,
            $testFirstName,
            $testLastName,
            $testEmail,
        );

        $template = '{"id":%s,"firstName":"%s","lastName":"%s","email":"%s"}';
        $actual = sprintf($template, $testId, $testFirstName, $testLastName, $testEmail);
        $this->assertEquals($actual, json_encode($user));
    }
}
