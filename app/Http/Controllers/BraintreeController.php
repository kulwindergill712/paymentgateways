<?php

namespace App\Http\Controllers;

use App\Traits\reply;
use Illuminate\Http\Request;

class BraintreeController extends Controller
{
    use reply;

    public function checkout(Request $request)
    {
        return $request->all();
    }

    public function createtoken()
    {
        $merchantkey = "6zjmscqfbrqy9x7m"; //ffrom dash boardclient
        // $token = "sandbox_cskt8b58_pkdgff5x9yf7s6xn";
        $public_key = "mhnkyxjyvr9r7gyp"; //from dash boardclient
        $private_key = "27b5baaed749782e5641542ae4e4413a"; //from dash boardclient
        $secret = base64_encode($public_key . ':' . $private_key);

        $url = 'https://payments.sandbox.braintree-api.com/graphql';

        $fields = '{"query": "mutation ExampleClientToken($input: CreateClientTokenInput) {
            createClientToken(input: $input) {
                clientToken
              }
          }" ,
        "variables": {
          "input": {

            "clientToken": {
              "merchantAccountId": "goteso"
            }
          }
        }}';

        $token = $this->Curl($url, $secret, $fields);

        $response = [
            'token' => $token['data']['createClientToken']['clientToken'],

        ];

        $t = $token['data']['createClientToken'];
        return view('brain', compact('response'));
    }
    public function charge(Request $request)
    {

        $public_key = "pvx6mx78vcfzw6zt";
        $private_key = "e5d066e77d506c4dc9df61d4126c4fae";
        $secret = base64_encode($public_key . ':' . $private_key);

        $url = 'https://payments.sandbox.braintree-api.com/graphql';
        $fields = '{"query": "mutation  ExampleCharge($input: ChargePaymentMethodInput!) {
            chargePaymentMethod(input: $input) {
                transaction {
                    id
                    status
                  }
              }
          }" ,
        "variables": {
          "input": {

            "paymentMethodId": "tokencc_bj_jgnzpk_hf7y5d_6xwz6n_gnn787_wcy",
            "transaction": {
              "amount": "1651.23"
            }
          }
        }}';

        return $this->Curl($url, $secret, $fields);
    }

    public function Curl($url, $secret, $fields)
    {

        $ch = curl_init();
        // $fields_encoded = json_encode($fields, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=utf-8",
            "Authorization:Basic $secret",
            "Braintree-Version: 2019-01-01",
        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }
}
