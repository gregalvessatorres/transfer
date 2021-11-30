<?php

use App\Models\Wallet;
use App\Service\WalletService;
use Illuminate\Http\Response;

class WalletServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = Mockery::mock(WalletService::class)->makePartial();

        parent::setUp();
    }

    public function testMustCreateNewWallet()
    {
        $mockWalletData      = [
            'user'    => $this->faker->randomDigit(),
            'balance' => $this->faker->randomFloat(999)
        ];
        $walletMock          = Mockery::mock(Wallet::class)->makePartial();
        $walletMock->user    = $mockWalletData['user'];
        $walletMock->balance = $mockWalletData['balance'];

        $this->service->shouldReceive('create')->andReturn($walletMock);

        $result = $this->service->create($mockWalletData);
        $this->assertInstanceOf(Wallet::class, $result);
        $this->assertEquals($walletMock, $result);
    }

    public function testMustReceiveErrorWhenCreateNewWallet()
    {
        $mockWalletData      = [
            'user'    => $this->faker->randomDigit()
        ];
        $walletMock       = Mockery::mock(Wallet::class)->makePartial();
        $walletMock->id   = $this->faker->randomDigit();
        $walletMock->user = $mockWalletData['user'];

        $this->expectExceptionMessage("User already has a Wallet : $walletMock->id");
        $walletMock
            ->shouldReceive('where')
            ->andReturn($walletMock);
        $this->service
            ->shouldReceive('create')
            ->andThrow("User already has a Wallet : $walletMock->id", Response::HTTP_BAD_REQUEST);

        $this->service->create($mockWalletData);
    }

    public function testMustReturnWalletsListByUser()
    {
        $expectedResult = [
            'id' => $this->faker->randomDigit(),
            'user_id' => $this->faker->randomDigit(),
            'name' => $this->faker->name,
            'email' =>$this->faker->email,
            'balance' =>$this->faker->randomFloat()
        ];

        $this->service->shouldReceive('walletsByUser')->andReturn($expectedResult);
        $result = $this->service->walletsByUser();
        $this->assertEquals($expectedResult, $result);
    }
}
