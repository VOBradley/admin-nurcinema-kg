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

    public static function fail($data, $message = null)
    {
        return [
            "success" => false,
            "data" => $data,
            "message" => $message
        ];
    }
}
