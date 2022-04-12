<?php
//require_once('../../vendor/autoload.php');

use CRM_Payment2c2p_ExtensionUtil as E;
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;


class CRM_Payment2c2p_PaymentTokenRequest
{
    public $secretkey = '';
    public $version = '';
    public $merchant_id = '';
    public $description = '';
    public $order_id = '';
    public $invoice_no = '';
    public $currency = '';
    public $amount = '';
    public $customer_email = '';
    public $pay_category_id = '';
    public $promotion = '';
    public $user_defined_1 = '';
    public $user_defined_2 = '';
    public $user_defined_3 = '';
    public $user_defined_4 = '';
    public $user_defined_5 = '';
    public $result_url_1 = '';
    public $result_url_2 = '';
    public $payment_option = '';
    public $enable_store_card = '';
    public $stored_card_unique_id = '';
    public $request_3ds = '';
    public $payment_expiry = '';
    public $default_lang = '';
    public $statement_descriptor = '';
    public $hash_value = '';
    public $frontendReturnUrl = '';

    /**
     * @param $secretkey
     * @param $payloadResponse
     * @return array
     */
    public static function getDecodedPayloadJWT($payloadResponse, $secretkey): array
    {
        $decodedPayload = JWT::decode($payloadResponse, $secretkey, array('HS256'));
        $decoded_array = (array)$decodedPayload;
        return $decoded_array;
    }
    public static function getDecodedPayload64($payloadResponse, $secretkey = ""): array
    {
        $decodedPayloadString = base64_decode($payloadResponse);
        $decodedPayload = json_decode($decodedPayloadString);
        $decoded_array = (array)$decodedPayload;
        return $decoded_array;
    }

    /**
     * @return string
     */
    public function getJwtData(): string
    {
        $payload = array(
            "merchantID" => $this->merchant_id,
            "invoiceNo" => $this->invoice_no,
            "description" => $this->description,
            "amount" => $this->amount,
            "currencyCode" => $this->currency,
            "frontendReturnUrl" => $this->frontendReturnUrl,
        );

        $jwt = JWT::encode($payload, $this->secretkey);

        $data = '{"payload":"' . $jwt . '"}';

        return $data;
    }

    /**
     * @param $secretkey
     * @param $response
     */
    public static function getDecodedTokenResponse($response, $secretkey, $responsetype = 'payload'): array
    {
        $decoded = json_decode($response, true);
        if (isset($decoded[$responsetype])) {
            $payloadResponse = $decoded[$responsetype];
            if ($responsetype == 'payload') {
                $decoded_array = self::getDecodedPayloadJWT($payloadResponse, $secretkey);
            }
            if ($responsetype == 'paymentResponse') {
                $decoded_array = self::getDecodedPayload64($payloadResponse);
            }
            return $decoded_array;
        } else {
            return $decoded;
        }
    }


    public static function getEncodedTokenResponse(string $url, string $payload): string
    {
        $client = new Client();

        $response = $client->request('POST', $url, [
            'body' => $payload,
            'headers' => [
                'Accept' => 'text/plain',
                'Content-Type' => 'application/*+json',
            ],
        ]);
        return $response->getBody();
    }

}