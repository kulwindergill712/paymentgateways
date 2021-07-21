<?php

namespace App\Http\Controllers;

use App\PaymentGatewayTransaction;
use App\Traits\reply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Razorpay2Controller extends Controller
{
    use reply;
    protected $api_key = 'rzp_test_xYWkubMRNmRXr1';
    protected $secret = 'ruG9NT77Gxmh8QcAO3yncUu9';

    public function charge()
    {

        $receiptId = Str::random(20);
        $url = 'https://api.razorpay.com/v1/orders';
        $fields = [
            "amount" => 500,
            "currency" => "INR",
            "receipt" => $receiptId,
            "payment_capture" => 1,

        ];

        $result = $this->Curl($url, $fields);

        $response = [
            'user_id' => 1,
            'receipt' => $result['receipt'],
            'orderId' => $result['id'],
            'razorpayId' => $this->api_key,
            'amount' => 5000,
            'name' => 'kulwinder',
            'currency' => 'INR',
            'email' => 'kulwindergill712@gmail.com',
            'contactNumber' => '9501360632',
            'address' => 'jhgtuyui',
            'description' => 'Testing description',
            'callback' => 'http://192.168.1.14/FlutterWave/public/api/raz2/payment-complete?id=' . $result['id'],
        ];
        $transaction = PaymentGatewayTransaction::create([
            'order_id' => $response['orderId'],
            'gateway_id' => $response['receipt'],
            'amount' => $response['amount'],
            'customer_id' => 1,
            'gateway_identifier' => 'razorpay',
            'payload' => 'ut',
            'checksum' => 'gh',
            'status' => '0',
            'callback_url' => "5",

        ]);

        return view('checkout', compact('response'));

    }

    public function Complete(Request $request)
    {

        $transaction = PaymentGatewayTransaction::where("order_id", $request->id)->first();
        $url = 'https://api.razorpay.com/v1/orders/' . $request->id;
        return $response = $this->getcurl($url);
        if ($response['status'] == 'paid') {
            $transaction->update([
                'status' => 'completed',
                'payload' => json_encode($response),
                'transaction_id' => $request->rzp_paymentid,

            ]);
            return $this->success("payment Successfull", "");

        } else {
            return $this->failed("payment not Success", "");
        }

    }

    public function Curl($url, $fields)
    {

        $ch = curl_init();
        $fields_encoded = json_encode($fields, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->api_key:$this->secret");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_encoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=utf-8",

        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

    public function getcurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->api_key:$this->secret");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json", "cache-control: no-cache",
        ));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }
}
