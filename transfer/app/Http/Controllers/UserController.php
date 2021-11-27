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
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'name'      => 'required',
                'email'     => 'required|email',
                'cpf_cnpj'  => 'required',
                'user_type' => 'required|in:' . implode(',', config('const.user_type')),
            ]);

            $user = $this->service->createUser($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json($this->simpleMessage($e->getMessage()), $e->getCode());
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

            return response()->json($this->simpleMessage($e->getMessage()), $e->getCode());
        }
        return response()->json($this->successMessageCheckIndex(), Response::HTTP_OK);
    }

}