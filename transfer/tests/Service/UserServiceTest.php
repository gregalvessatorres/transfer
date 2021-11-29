<?php

use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Response;

class UserServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = Mockery::mock(UserService::class)->makePartial();

        parent::setUp();
    }

    public function testMustCreateNewUser()
    {
        $mockUserData   = [
            'name'      => $this->faker->name,
            'email'     => $this->faker->email,
            'cpf_cnpj'  => $this->faker->numerify('######'),
            'user_type' => rand(1, 2)
        ];
        $userMock       = Mockery::mock(User::class)->makePartial();
        $userMock->name = $mockUserData['name'];

        $this->service->shouldReceive('createUser')->andReturn($userMock);

        $result = $this->service->createUser($mockUserData);
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userMock, $result);
    }

    public function testMustReceiveErrorWhenCreateNewUser()
    {
        $this->expectExceptionMessage('Error creating user');
        $mockUserData = [
            'name'      => $this->faker->name,
            'email'     => $this->faker->email,
            'cpf_cnpj'  => $this->faker->numerify('######'),
            'user_type' => rand(1, 2)
        ];
        $userMock     = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('create')->andReturnNull();
        $this->service->shouldReceive('createUser')->andThrows(Exception::class, 'Error creating user',
                                                               Response::HTTP_NOT_FOUND);

        $this->service->createUser($mockUserData);
    }

    public function testMustDestroyUser()
    {
        $userMock     = Mockery::mock(User::class)->makePartial();
        $userMock->id = $this->faker->randomDigit();
        $userMock->shouldReceive('delete')->andReturnTrue();
        $this->service->shouldReceive('destroy')->andReturnNull();
        $this->service->destroy($userMock->id);
    }

    public function testMustReceiveErrorWhenDeletingUser()
    {
        $this->expectExceptionMessage('Users wallet must be removed first');
        $userMock     = Mockery::mock(User::class)->makePartial();
        $userMock->id = $this->faker->randomDigit();
        $userMock->shouldReceive('find')->andReturnSelf();
        $this->service->shouldReceive('destroy')->andThrows(Exception::class, 'Users wallet must be removed first',
                                                               Response::HTTP_NOT_FOUND);
        $this->service->destroy($userMock->id);
    }
}
