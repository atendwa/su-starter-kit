<?php

namespace App\Api\Mpesa\Core;

use Illuminate\Support\Collection;

class FetchAttributes
{
    public static function index(array $response): Collection
    {
        $success = array_key_exists('ResponseCode', $response);

        $OCID = 'OriginatorConversationID';

        $desc = 'ResponseDescription';

        return collect([
            'MerchantRequestID' => $response['MerchantRequestID'] ?? null,
            'CheckoutRequestID' => $response['CheckoutRequestID'] ?? null,
            'CustomerMessage' => $response['CustomerMessage'] ?? null,
            'ConversationID' => $response['ConversationID'] ?? null,
            'ResponseCode' => $response['ResponseCode'] ?? null,
            'errorMessage' => $response['errorMessage'] ?? null,
            'requestId' => $response['requestId'] ?? null,
            'errorCode' => $response['errorCode'] ?? null,
            $desc => $response[$desc] ?? null,
            $OCID => $response[$OCID] ?? null,
            'success' => $success,
        ]);
    }
}
