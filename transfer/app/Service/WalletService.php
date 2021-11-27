<?php

namespace App\Service;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function create(array $data): ?Wallet
    {
        $this->userHasWallet($data);
        return Wallet::create($data);
    }

    private function userHasWallet(array $data): void
    {
        $wallet = Wallet::where(['user' => $data['user']])->first();
        if($wallet){
            throw new \Exception("User already has a Wallet : $wallet->id", Response::HTTP_BAD_REQUEST);
        }
    }

    public function walletsByUser(): array
    {
        return Wallet::select(['wallets.id', DB::raw('users.id AS user_id'), 'users.name', 'users.email', 'wallets.balance'])
            ->join('users', 'wallets.user', 'users.id')
            ->get()
            ->toArray();
    }
}