<?php

namespace App\Traits;

trait BaseTrait
{
    public static function notFoundMessage(): array
    {
        return [
            'message' => 'Item not found'
        ];
    }

    public function successMessageCheckIndex(): array
    {
        $baseRoute = strtolower(class_basename($this->model()));
        return [
            'message'  => 'Success',
            'check_it' => route($baseRoute . "_get")
        ];
    }

    public function simpleMessage(string $message): array
    {
        return [
            'message' => $message
        ];
    }
}