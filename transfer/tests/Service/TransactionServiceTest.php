<?php

use App\Models\{Transaction, User, Wallet};
use App\Service\TransactionService;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;

class TransactionServiceTest extends TestCase
{
    private $service;
    private $payerWallet;
    private $payeeWallet;
    private $mockPayer;
    private $mockPayee;
    private $mockTransaction;
    private $guzzleClientMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service          = Mockery::mock(TransactionService::class)->makePartial();
        $this->payerWallet      = Mockery::mock(Wallet::class)->makePartial();
        $this->payeeWallet      = Mockery::mock(Wallet::class)->makePartial();
        $this->mockPayer        = Mockery::mock(User::class)->makePartial();
        $this->mockPayee        = Mockery::mock(User::class)->makePartial();
        $this->mockTransaction  = Mockery::mock(Transaction::class)->makePartial();
        $this->guzzleClientMock = Mockery::mock(Client::class)->makePartial();

        $this->payerWallet->user = $this->mockPayer;
        $this->payeeWallet->user = $this->mockPayee;

        $this->guzzleClientMock->shouldReceive('request')
            ->with($this->faker->url)
            ->andReturn(['message' => 'Autorizado']);

        $this->guzzleClientMock
            ->shouldReceive('getBody')
            ->withAnyArgs()
            ->andReturn(['message' => 'Autorizado']);
    }

    public function testMustCreateNewTransaction()
    {
        Queue::shouldReceive('push');
        $transactionData            = $this->createInitialData();
        $this->mockPayer->id        = $transactionData['payer'];
        $this->mockPayer->user_type = config('const.user_type.normal');
        $this->mockPayee->id        = $transactionData['payee'];
        $this->payerWallet->balance = $transactionData['value'];

        $this->payerWallet->shouldReceive('where')->andReturnSelf();
        $this->payeeWallet->shouldReceive('where')->andReturnSelf();
        $this->mockTransaction->shouldReceive('create')->andReturnSelf();
        $this->service->shouldReceive('performTransaction')->andReturn($this->mockTransaction);

        $result = $this->service->performTransaction($transactionData);
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($this->mockTransaction, $result);
    }

    public function testMustReceiveErrorWhenNotFindWallets()
    {
        $this->expectExceptionMessage('One or both users have no wallet with funds');
        $transactionData = $this->createInitialData();
        $this->payerWallet
            ->shouldReceive('where')
            ->andReturnNull();

        $this->service
            ->shouldReceive('performTransaction')
            ->andThrows(Exception::class, 'One or both users have no wallet with funds', Response::HTTP_BAD_REQUEST);

        $this->service->performTransaction($transactionData);
    }

    public function testMustReceiveErrorWhenNotFindUsers()
    {
        $this->expectExceptionMessage('One or both users not found');
        $transactionData = $this->createInitialData();
        $this->payerWallet->shouldReceive('where')->andReturnSelf();
        $this->payeeWallet->shouldReceive('where')->andReturnSelf();
        $this->payerWallet->shouldReceive('user')->andReturnNull();

        $this->service
            ->shouldReceive('performTransaction')
            ->andThrows(Exception::class, 'One or both users not found', Response::HTTP_BAD_REQUEST);

        $this->service->performTransaction($transactionData);
    }

    public function testMustReceiveErrorWhenPayerIsAStore()
    {
        $this->expectExceptionMessage('This type of user cannot transfer funds to another user');
        $transactionData = $this->createInitialData();
        $this->payerWallet->shouldReceive('where')->andReturnSelf();
        $this->payeeWallet->shouldReceive('where')->andReturnSelf();
        $this->payerWallet->user->user_type = 2;

        $this->service
            ->shouldReceive('performTransaction')
            ->andThrows(Exception::class, 'This type of user cannot transfer funds to another user',
                        Response::HTTP_BAD_REQUEST);

        $this->service->performTransaction($transactionData);
    }

    public function testMustReceiveErrorWhenPayerHasNoEnoughFunds()
    {
        $this->expectExceptionMessage('Not enough funds');
        $transactionData = $this->createInitialData();
        $this->payerWallet->shouldReceive('where')->andReturnSelf();
        $this->payeeWallet->shouldReceive('where')->andReturnSelf();

        $this->payerWallet->balance = $transactionData['value'] - 10;

        $this->service
            ->shouldReceive('performTransaction')
            ->andThrows(Exception::class,'Not enough funds', Response::HTTP_BAD_REQUEST);

        $this->service->performTransaction($transactionData);
    }

    /**
     * @return array
     */
    private function createInitialData(): array
    {
        $transactionData = [
            'payer' => $this->faker->randomNumber(1, 99),
            'payee' => $this->faker->randomNumber(1, 99),
            'value' => $this->faker->randomFloat(3)
        ];

        return $transactionData;
    }
}
