<?php

namespace App\Api\Mpesa\Core;

use App\Models\MpesaTransaction;

class CreateMpesaTransaction
{
    public static function index($attributes): MpesaTransaction
    {
        $oci = $attributes->get('OriginatorConversationID');

        $transaction = new MpesaTransaction();

        $transaction->customer_message = $attributes->get('CustomerMessage');

        $transaction->conversation_id = $attributes->get('ConversationID');

        $transaction->response_code = $attributes->get('ResponseCode');

        $transaction->error_message = $attributes->get('errorMessage');

        $transaction->error_code = $attributes->get('errorCode');

        $transaction->request_id = $attributes->get('requestId');

        $transaction->initiated = $attributes->get('success');

        $transaction->originator_conversation_id = $oci;

        $transaction->response_description = $attributes
            ->get('ResponseDescription');

        $transaction->merchant_request_id = $attributes
            ->get('MerchantRequestID');

        $transaction->checkout_request_id = $attributes
            ->get('CheckoutRequestID');

        $transaction->user_id = auth()->user()?->id;

        return $transaction;
    }
}
