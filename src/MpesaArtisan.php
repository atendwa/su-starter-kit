<?php

declare(strict_types=1);

namespace Atendwa\MpesaArtisan;

use Atendwa\MpesaArtisan\Exceptions\InvalidAmount;
use Atendwa\MpesaArtisan\Support\SanitisePhoneNumber;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

final class MpesaArtisan
{
    private bool $useConsumerCredentials = true;

    /**
     * @throws ConnectionException
     */
    public function authorization(string $key, string $secret)
    {
        return Http::withBasicAuth($key, $secret)
            ->baseUrl($this->fetch_base_url())
            ->get(config('mpesa.endpoints.access_token'))
            ->collect()
            ->get('access_token');
    }

    public function fetch_base_url(): string
    {
        return match (config('mpesa.environment')) {
            default => config('mpesa.urls.sandbox'),
            'live' => config('mpesa.urls.live'),
        };
    }

    /**
     * @throws ConnectionException
     */
    public function dynamic_qr(): Collection
    {
        //        todo:
        $url = config('mpesa.endpoints.dynamic_qr');

        $body = [
            'MerchantName' => 'TEST SUPERMARKET',
            'RefNo' => 'Invoice Test',
            'Amount' => 1,
            'TrxCode' => 'BG',
            'CPI' => '373132',
            'Size' => '300',
        ];

        return $this->send_request($url, $body);
    }

    /**
     * @throws Throwable
     */
    public function mpesa_express(
        int $amount,
        string $phoneNumber,
        string $description,
        ?string $accountReference = null,
        ?string $callback = null
    ): Collection {
//        todo:
        throw_if($amount < 1, new InvalidAmount());

        $endpoint = config('mpesa.endpoints.mpesa_express');

        $phoneNumber = SanitisePhoneNumber::index($phoneNumber);

        $callback = $callback ?? config('mpesa.callback.default');

        $body = [
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'BusinessShortCode' => config('mpesa.business_shortcode'),
            'AccountReference' => $accountReference ?? config('mpesa.business_shortcode'),
            'PartyB' => config('mpesa.business_shortcode'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Password' => $this->generate_password(),
            'TransactionDesc' => $description,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $callback,
            'PartyA' => $phoneNumber,
            'Amount' => $amount,
        ];

        return $this->send_request($endpoint, $body);
    }

    //    public function c2b(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function b2c(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function transaction_status(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function account_balance(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function reversal(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function tax_remittance(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function business_pay_bill(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function business_buy_goods(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function bill_manager(): void
    //    {
    //        $endpoint = config('');
    //    }
    //
    //    public function b2b_express_checkout(): void
    //    {
    //        $endpoint = config('');
    //    }

    /**
     * @throws ConnectionException
     */
    public function send_request(string $url, array $body): Collection
    {
        $token = $this->authorization(...$this->fetch_credentials());

        return Http::withToken($token)
            ->baseUrl($this->fetch_base_url())
            ->acceptJson()
            ->post($url, $body)
            ->collect();
    }

    public function generate_password(): string
    {
        return base64_encode(
            config('mpesa.business_shortcode') .
            config('mpesa.passkey') .
            Carbon::rawParse('now')->format('YmdHms')
        );
    }

    private function fetch_credentials(): array
    {
        $key = match ($this->useConsumerCredentials) {
            true => config('mpesa.credentials.consumer.key'),
            false => config('mpesa.credentials.payment.key'),
        };

        $secret = match ($this->useConsumerCredentials) {
            true => config('mpesa.credentials.consumer.secret'),
            false => config('mpesa.credentials.payment.secret'),
        };

        return [$key, $secret];
    }
}
