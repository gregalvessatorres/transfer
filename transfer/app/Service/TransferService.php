<?php

namespace App\Service;

use App\Models\Wallet;

class TransferService
{

    public function validateTransfer(): bool
    {
        return true;
    }

    public function transferFunds(array $data): void
    {
        $payerWallet = Wallet::where(['user'=>$data['payer']])->first();
        $payeeWallet = Wallet::where(['user'=>$data['payee']])->first();
        $payerWallet->ballance = $payerWallet->ballance - $data['value'];
        $payeeWallet->ballance = $payeeWallet->ballance + $data['value'];
        $payeeWallet->save();
        $payerWallet->save();
    }
}