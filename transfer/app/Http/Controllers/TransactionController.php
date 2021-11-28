<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Service\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TransactionController extends BaseController
{
    protected $service;
    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function model()
    {
        return Transaction::class;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|numeric|min:1',
            'payer' => 'required|different:payee|exists:users,id',
            'payee' => 'required|different:payer|exists:users,id',
        ]);
        DB::beginTransaction();
        try {
            $transaction = $this->service->performTransaction($request->all());
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json($this->simpleMessage($e->getMessage()), $e->getCode());
        }
        return response()->json($transaction, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->service->destroy($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json($this->simpleMessage($e->getMessage()), $e->getCode());
        }
        return response()->json($this->successMessageCheckIndex(), Response::HTTP_OK);
    }
}