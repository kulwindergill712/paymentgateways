<?php

namespace App\Http\Controllers;

use App\PaymentGatewayTransaction;
use App\Traits\reply;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Psy\Util\Json;

class MonriController extends Controller
{
    protected $key = 'G0nZaII2:.kgv';
    protected $secret_key = 'fb5115962eb30d5abf47cb0bd57e828c102cc871';
    use reply;
    public function generate_payment(Request $request)
    {

        $url = 'https://ipgtest.monri.com/v2/terminal-entry/create-or-update';
        $id = 'random' . time();

        $customer_data = User::where('id', 1)->first();
        $transaction = PaymentGatewayTransaction::create([
            'gateway_id' => "g",
            'amount' => 100 * 100,
            'customer_id' => $id,
            'gateway_identifier' => 'morni',
            'order_id' => $id,
            'payload' => 'ut',
            'checksum' => 'gh',
            'status' => '45',
            'callback_url' => $request->filled('callback_url') ? $request->callback_url : '',
        ]);
        $fields = [
            'amount' => 100,
            'order_number' => $id,
            'currency' => 'EUR',
            'transaction_type' => 'purchase',
            'order_info' => 'Create payment session order info',
            'scenario' => 'charge',

        ];
        $response = $this->Curl($url, $fields);
        if ($response['status'] == 'created') {
            $res['token'] = $id;
            $res['payment_page'] = $response['payment_url'];
            $res['listen_page'] = 'http://192.168.1.14/FlutterWave/public/api/monri/callback';
            $res['confirm_url'] = 'http://192.168.1.14/FlutterWave/public/api/monri/verify/' . $id;
            return $res;
        } else {
            return $this->failed($response);
        }

    }

    public function callbackPage(Request $request)
    {
        $data = $request->all();

        $transaction = PaymentGatewayTransaction::where('order_id', $data['order_number'])->first();

        if ($data['response_code'] == '0000') {

            $transaction->update([
                'status' => 1,
                'checksum' => $data['digest'],
                'gateway_id' => $data['approval_code'],

            ]);

        } else {
            return $this->failed("some error occor during payment");
        }

        if ($transaction->callback_url != '') {
            return Redirect::to($transaction->callback_url);
        }
        return 1;

    }
    public function verifytransaction(Request $request, $id)
    {
        return 0;
        $url = 'https://ipgtest.monri.com/v2/terminal-entry/' . $id . '/show';
        $response = $this->getCurl($url);

        $transaction_data = PaymentGatewayTransaction::where('order_id', $id)->first();
        if ($transaction_data->status == '1') {
            if ($response['terminal_entry_status'] == 'approved') {
                return $this->success('Payment Successful.', "");
            } else {
                return $this->failed('Payment Failed from server response.');
            }
            return $this->success('Payment Successful.', "");
        } else {

            return $this->failed('Payment Failed.');}

    }

    public function Curl($url, $fields)
    {
        $body_as_string = Json::encode($fields);
        $timestamp = time();
        $digest = hash('sha512', $this->key . $timestamp . $this->secret_key . $body_as_string);
        $authorization = "WP3-v2 $this->secret_key $timestamp $digest";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_as_string);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body_as_string),
            'Authorization: ' . $authorization,
        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

    public function getCurl($url)
    {
        $timestamp = time();
        $digest = hash('sha512', $this->key . $timestamp . $this->secret_key);
        $authorization = "WP3-v2 $this->secret_key $timestamp $digest";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json", "cache-control: no-cache",
            'Authorization: ' . $authorization,
        ));
        return $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
    }
}
