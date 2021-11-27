<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Service\WalletService;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\DB;

class WalletController extends BaseController
{

    /**
     * @var WalletService
     */
    private $service;

    public function __construct(WalletService $service)
    {
        $this->service = $service;
    }

    public function model()
    {
        return Wallet::class;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'user'    => 'required|exists:users,id',
                'balance' => 'sometimes|numeric|min:0'
            ]);

            $wallet = $this->service->create($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return \response()->json($this->simpleMessage($e->getMessage()), $e->getCode());
        }
        return response()->json($wallet, Response::HTTP_OK);
    }

    public function index()
    {
        $wallets = $this->service->walletsByUser();

        return response()->json($wallets, Response::HTTP_OK);
    }
}