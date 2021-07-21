<?php

namespace App\Http\Controllers\Api3\ThirdParty;

use App\Http\Controllers\Controller;
use App\PaymentGatewayTransaction;
use App\Traits\goteso;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MpesaController extends Controller
{
    use goteso;
    protected $secret_key = "FLWSECK_TEST-0b95a3f54671b911dcb604cc267f7901-X"; //test
    public function generate_token(Request $request)
    {
        $callback_url = env('APP_URL', '') . '/c/flutterwave/callback';
        $validator = Validator::make($request->all(), [

            'amount' => 'required|numeric|min:0',
            'phone' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->kFailed($validator->errors()->first());
        }

        if ($request->amount == 0) {
            return $this->kFailed('Please Choose Cash Payment Option for 0 Value Transactions.');
        }

        $url = "https://api.flutterwave.com/v3/charges?type=mpesa";
        $customer_data = User::where('id', 1)->first();
        $id = floor(time() - 999999999);
        $transaction = PaymentGatewayTransaction::create([
            'gateway_id' => $id,
            'amount' => $request->amount,
            'customer_id' => 1,
            'gateway_identifier' => 'mpesa',

        ]);

        $fields = [
            "tx_ref" => $id,
            "amount" => $transaction->amount,
            "currency" => "KES",
            "email" => $customer_data->email,
            "phone_number" => $request->phone,

        ];
        return $response = $this->Curl($url, $fields);
        if ($response['status'] == 'success') {
            $transaction->update([
                'status' => 'completed',
                'transaction_id' => $response['data']['id'],
                'payload' => json_encode($response),
            ]);
            return $this->kSuccess("Payment Done Successfully");
        }

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
    public function verify(Request $request, $id)
    {

        $transaction_data = PaymentGatewayTransaction::where('gateway_id', $id)->first();

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

        if ($response['data']['status'] == 'successful') {
            return $this->kSuccess('Payment Successful.');
        } else {
            return $this->kFailed('Payment Failed.');
        }

    }

}
