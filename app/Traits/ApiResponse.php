<?php

namespace App\Traits;

trait ApiResponse
{
    public function response(array|object $data = null, string $message = null, int $code = 200)
    {
        return response([
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}
