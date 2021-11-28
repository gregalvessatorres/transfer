<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    /**
     * @var UserService
     */
    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function model(): string
    {
        return User::class;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'cpf_cnpj'  => 'required|unique:users,cpf_cnpj',
            'user_type' => 'required|in:' . implode(',', config('const.user_type')),
        ]);

        DB::beginTransaction();
        try {
            $user = $this->service->createUser($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json($this->simpleMessage($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($user, Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $this->service->destroy($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json($this->simpleMessage($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
        return response()->json($this->successMessageCheckIndex(), Response::HTTP_OK);
    }

}