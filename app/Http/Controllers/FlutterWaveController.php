<?php

namespace App\Http\Controllers;

use App\PaymentGatewayTransaction;
use App\Traits\reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlutterWaveController extends Controller
{
    use reply;
    protected $public_key = 'FLWPUBK_TEST-e6bb1b95364deb62eccab500ec12f921-X';
    protected $secret_key = "FLWSECK-5bc3d6c4cc5a2ae94dd3294505475355-X";
    protected $encription_key = "FLWSECK_TESTeb9511b0ba68";
    protected $api_endpoint = "https://api.flutterwave.com/v3";

    public function generate_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => "required",
            'amount' => 'required|numeric|min:0|gt:0',
        ]);
        if ($validator->errors()->all()) {return $this->failed($validator->errors()->first());}
        $url = $this->api_endpoint . "/payments";
        $id = floor(time() - 999999999);
        // $customer_data = User::where('id', $request->customer_id)->first();
        // $transaction = PaymentGatewayTransaction::create([
        //     'tx_id' => $id,
        //     'amount' => $request->amount,
        //     'customer_id' => $request->customer_id,
        //     'gateway_type' => 'flutterwave',
        // ]);
        $fields = [
            "tx_ref" => $id,
            "amount" => $request->amount,
            "currency" => "KES",

            "country" => "KES",
            "redirect_url" => "http://192.168.1.11/FlutterWave/public/api/flutterwave/success",

            "meta" => [
                "consumer_id" => 23,
                "consumer_mac" => "92a3-912ba-1192a",
            ],
            "customer" => [
                "email" => "kulwindrgill712@gmail.com",
                "phonenumber" => "08102909304",
                "name" => 'gk',
            ],
            "customizations" => [
                "title" => "Pied Piper Payments",
                "description" => "Middleout isn't free. Pay the price",
                "logo" => "https://assets.piedpiper.com/logo.png",
            ],

        ];

        return $response = $this->Curl($url, $fields);

        // $res['token'] = $transaction->tx_id;
        return $res['payment_page'] = $response['data']['link'];
        $res['listen_page'] = env('APP_URL', '') . '/c/paygate/callback';
        // $res['confirm_url'] = env('APP_URL', '') . '/c/paygate/confirm/' . $transaction->id;
        // return $this->kSuccess('Token Generated Successfully.', $res);

        // if ($response['status'] == 'success') {
        //     $transaction->update([
        //         'payment_link' => $response['data']['link'],

        //     ]);
        //     return $this->success("payment link generated succesfully", $response['data']['link']);
        // } else {
        //     return $this->failed($response);
        // }

    }

    public function verify(Request $request)
    {

        $transaction = PaymentGatewayTransaction::where('tx_id', $request->tx_ref)->first();
        if ($request->status == 'successful') {
            $transaction->update([
                'status' => 1,
                'transaction_id' => $request->transaction_id,

            ]);

        } else {
            return $this->failed("some error occor during payment");
        }

    }

    public function verifytransaction(Request $request)
    {

        $tx_ref = $request->transaction_id;
        $transaction_data = PaymentGatewayTransaction::where('tx_id', $tx_ref)->first();

        $url = "https://api.flutterwave.com/v3/transactions/ $transaction_data->transaction_id/verify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json", "cache-control: no-cache",
            "Authorization:Bearer $this->secret_key",
        ));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;

    }

    public function Curl($url, $fields)
    {

        $ch = curl_init();
        $fields_encoded = json_encode($fields, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_encoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=utf-8",
            "Authorization:Bearer $this->secret_key",
        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

}
