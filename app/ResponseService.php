<?php

namespace App;

class ResponseService
{
    public static function success($data, $message = null)
    {
        return [
            "success" => true,
            "data" => $data,
            "message" => $message
        ];
    }

    public static function fail($data, $message = null, $status = 500)
    {
        return response()->json([
            "success" => false,
            "data" => $data,
            "message" => $message
        ])->setStatusCode($status ?? 500);
    }
}
