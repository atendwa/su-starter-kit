<?php

namespace App\Api\Mpesa\Actions;

use Illuminate\Http\Response;

class ValidationResponse
{
    public function index(): Response
    {
        $response = new Response();

        $response->headers->set(
            'Content-Type',
            'application/json; charset=utf-8'
        );

        return $response->setContent(json_encode([
            'ResultDesc' => 'Accepted validation request',
            'ResultCode' => '0',
        ]));
    }
}
