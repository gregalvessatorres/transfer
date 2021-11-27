<?php

namespace App\Service;

use App\Jobs\TransactionNotificationJob;
use App\Models\{Transaction, User, Wallet};
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;

class TransactionService
{
    public function performTransaction(array $data): Transaction
    {
        [$payerWallet, $payeeWallet] = $this->findWallets($data);
        [$payer, $payee] = $this->findUsers($payerWallet, $payeeWallet);

        $this->executeBasicValidations($payerWallet, $data['value']);

        $this->executeTransferOfFundsOnTheWallets($payerWallet, $data['value'], $payeeWallet);
        $transaction = $this->saveTransaction($payer, $payee, $data['value']);

        $this->checkTransactioExternalAuthorization();
        $this->putUserNotificationInQueue(array_merge($data, $payer->toarray(), $payee->toarray()));

        return $transaction;
    }

    private function findWallets(array $data): array
    {
        $payerWallet = Wallet::where(['user' => $data['payer']])->first();
        $payeeWallet = Wallet::where(['user' => $data['payee']])->first();

        if (is_null($payerWallet) || is_null($payeeWallet)) {
            throw new \Exception('One or both users have no wallet', Response::HTTP_BAD_REQUEST);
        }

        return [$payerWallet, $payeeWallet];
    }

    private function findUsers(Wallet $payerWallet, Wallet $payeeWallet): array
    {
        $payer = $payerWallet->user()->first();
        $payee = $payeeWallet->user()->first();
        if (is_null($payer) || is_null($payee)) {
            throw new \Exception('One or both users not found', Response::HTTP_BAD_REQUEST);
        }

        return [$payer, $payee];
    }

    private function executeBasicValidations($payerWallet, $value): void
    {
        $this->checkIfUserCanTransferFunds($payerWallet);
        $this->validateFundsForTransfer($payerWallet, $value);
    }

    public function checkTransactioExternalAuthorization(): void
    {
        $guzzleGlient = new Client();
        $mockUrl      = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $response     = $guzzleGlient->request('GET', $mockUrl);
        $data         = json_decode($response->getBody());
        if ($data->message != config('const.authorization_message')) {
            throw new \Exception('Transaction not authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    private function checkIfUserCanTransferFunds(Wallet $payerWallet): void
    {
        $user = $payerWallet->user()->first();
        if ($user->user_type == config('const.user_type.store')) {
            throw new \Exception('This type of user cannot transfer funds to another user',
                                 Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateFundsForTransfer(Wallet $payerWallet, float $value): void
    {
        if (($payerWallet->balance - $value) < 0) {
            throw new \Exception('Not enough funds', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $payerWallet
     * @param $value
     * @param $payeeWallet
     */
    private function executeTransferOfFundsOnTheWallets(Wallet $payerWallet, float $value, Wallet $payeeWallet): void
    {
        $payerWallet->balance = $payerWallet->balance - $value;
        $payeeWallet->balance = $payeeWallet->balance + $value;
        $payeeWallet->save();
        $payerWallet->save();
    }

    private function saveTransaction(User $payer, User $payee, float $value): Transaction
    {
        return Transaction::create([
                                       'payer' => $payer->id,
                                       'payee' => $payee->id,
                                       'value' => $value
                                   ]);
    }

    private function putUserNotificationInQueue(array $transactionData): void
    {
        Queue::push(new TransactionNotificationJob($transactionData));
    }
}
