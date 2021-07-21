<?php

namespace App\Http\Controllers;

use App\Traits\reply;
use Illuminate\Http\Request;

class HyperSplitController extends Controller
{
    use reply;
    protected $entityId = '8a8294174d0595bb014d05d829cb01cd';

    protected $bearerToken = 'OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA==';
    // protected $bearerToken = 'OGFjN2E0Yzg3MmJkMmYyZjAxNzJjMjZlYmM2YjBmYzR8QUtXRFE5Y05IMg=='; //obtained by client

    protected $baseUrl = 'https://test.oppwa.com';
    public function token(Request $request)
    {
        $url = $this->baseUrl . "/v1/checkouts";
        $data = [
            "entityId" => $this->entityId,
            "amount" => 1000,
            "currency" => "INR",
            "paymentType" => "DB",
        ];
        $data = http_build_query($data);
        $result = $this->Curl($url, 'POST', $data);
        return view('hyperpay')->with('data', [
            'callback_url' => env('APP_URL', '') . '/c/hyperpay/callback',
            'checkout_id' => $result['id'],
        ]);
    }
    function new (Request $request) {
        return view('new');
    }

    public function callback(Request $request)
    {
        $resource = $request['resourcePath'];
        $res = stripslashes($resource);

        $url = $this->baseUrl . $res;
        $url .= "?entityId=" . $this->entityId;
        return $response = $this->Curl($url, 'GET');

        $transaction = \App\PaymentGatewayTransaction::where('gateway_id', $response['ndc'])->first();

        if ('000.100.110' === $response['result']['code']) {
            $transaction->update([
                'payload' => json_encode($response),
                'transaction_id' => $response['id'],
                'status' => 'completed',
            ]);
        } else {
            if ($transaction && $transaction->status == 'pending') {
                $transaction->update([
                    'transaction_id' => $response['id'],
                    'payload' => json_encode($response),
                    'status' => 'failed',
                ]);
            }
        }
        return 1;
    }

    public function Curl($url, $method = 'POST', $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $this->bearerToken));

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($responseData, true);
    }

}
