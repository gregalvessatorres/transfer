<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Service\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    protected $service;
    public function __construct(TransferService $service)
    {
        $this->service = $service;
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|min:1',
            'payer' => 'required|exists:users,id',
            'payee' => 'required|exists:users,id',
        ]);

        //validar de acrodo com o que é necessário no teste.

        $this->service->validateTransfer();
        $this->service->transferFunds($request->all());
    }
}