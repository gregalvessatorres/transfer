<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::select(['users.id', 'users.name', 'users.email', 'wallets.ballance'])
            ->join('users', 'wallets.user', 'users.id')
            ->get();

        return response()->json($wallets->all());
    }
}