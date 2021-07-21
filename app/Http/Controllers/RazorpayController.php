<?php

namespace App\Http\Controllers;

use App\PaymentGatewayTransaction;
use App\Traits\reply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    use reply;
    public function charge()
    {
        $api_key = 'rzp_test_xYWkubMRNmRXr1';
        $secret = 'ruG9NT77Gxmh8QcAO3yncUu9';

        $receiptId = Str::random(20);
        $api = new Api($api_key, $secret);
        $order = $api->order->create(array(
            'receipt' => $receiptId,
            'amount' => 5000,
            'currency' => 'INR',
        )
        );
        $response = [
            'user_id' => 1,
            'receipt' => $order['receipt'],
            'orderId' => $order['id'],
            'razorpayId' => $api_key,
            'amount' => 5000,
            'name' => 'kulwinder',
            'currency' => 'INR',
            'email' => 'kulwindergill712@gmail.com',
            'contactNumber' => '9501360632',
            'address' => 'jhgtuyui',
            'description' => 'Testing description',
            'callback' => 'http://192.168.1.14/FlutterWave/public/api/raz/payment-complete?id=' . $order['receipt'],
        ];
        $transaction = PaymentGatewayTransaction::create([
            'order_id' => $order['id'],
            'gateway_id' => $order['receipt'],
            'amount' => 500,
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

        $transaction = PaymentGatewayTransaction::where('gateway_id', $request->id)->first();

        $transaction->update([
            'transaction_id' => $request->all()['rzp_paymentid'],
            'payload' => $request->all()['rzp_signature'],
        ]);
        $secret = 'ruG9NT77Gxmh8QcAO3yncUu9';

        $generated_signature = hash_hmac('sha256', $transaction->order_id . "|" . $request->all()['rzp_paymentid'], $secret);

        if ($generated_signature == $transaction->payload) {

            $signatureStatus = $this->SignatureVerify(
                $request->all()['rzp_signature'],
                $request->all()['rzp_paymentid'],
                $request->all()['rzp_orderid']
            );

            if ($signatureStatus == true) {
                $transaction->update([
                    'status' => 'completed',

                ]);
                return $this->success("payment verified successfully", "");
            } else {
                $transaction->update([
                    'status' => 'failed',

                ]);
                return $this->success("payment not verified successfully", "");
            }
        } else {
            return 0;
        }

    }

    private function SignatureVerify($_signature, $_paymentId, $_orderId)
    {
        try
        {
            $api_key = 'rzp_test_xYWkubMRNmRXr1';
            $secret = 'ruG9NT77Gxmh8QcAO3yncUu9';
            $api = new Api($api_key, $secret);

            $attributes = array('razorpay_signature' => $_signature, 'razorpay_payment_id' => $_paymentId, 'razorpay_order_id' => $_orderId);
            $order = $api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {

            return false;
        }
    }
}
