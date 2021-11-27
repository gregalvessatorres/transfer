<?php

namespace App\Jobs;

use App\Service\TransactionService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TransactionNotificationJob extends Job
{

    /**
     * @var TransactionService
     */
    private $transactionData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $transactionData)
    {
        $this->transactionData = $transactionData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->notifyUsers();
    }

    private function notifyUsers(): void
    {
        Log::debug('User was notified, data:', $this->transactionData);
        $guzzleGlient = new Client();
        $mockUrl      = 'http://o4d9z.mocklab.io/notify';
        $response     = $guzzleGlient->request('GET', $mockUrl);
        $responseData = json_decode($response->getBody(), true);
        Log::debug('Response Data', $responseData);
    }
}
