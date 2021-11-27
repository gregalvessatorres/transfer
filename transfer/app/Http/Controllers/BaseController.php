<?php

namespace App\Http\Controllers;

use App\Traits\BaseTrait;
use Illuminate\Http\Response;

abstract class BaseController extends Controller
{
    use BaseTrait;

    abstract public function model();

    public function index()
    {
        return response()->json($this->model()::all()->toArray(), Response::HTTP_OK);
    }

    public function show(int $id)
    {
        $model = $this->model()::find($id);
        if (is_null($model)) {
            return response()->json(self::notFoundMessage(), Response::HTTP_NOT_FOUND);
        }

        return $model;
    }

    public function destroy(int $id)
    {
        if (!$this->model()::destroy($id)) {
            return response()->json(self::notFoundMessage(), Response::HTTP_NOT_FOUND);
        }

        return response()->json(
            $this->successMessageCheckIndex(),
            Response::HTTP_OK);
    }
}