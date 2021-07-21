<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psy\Util\Json;

class RjbankController extends Controller
{

    public function token(Request $request)
    {
        $data = [
            "amt" => "1000.00",
            "action" => "1",
            "id" => "2h9z2KeKZ2NV9ra",
            "password" => '$$SBR8fmAz6$0e3',
            "currencyCode" => "682",
            "trackId" => (string) time(),
            "responseURL" => "http://192.168.1.19/FlutterWave/public/api/rj/success",
            "errorURL" => "http://192.168.1.19/FlutterWave/public/api/rj/fail",

        ];
        $data = array($data);
        $key = '10637214941710637214941710637214';

        $str = Json::encode($data);

        $transport = $this->encryptAES($str, $key);

        $fields = [
            "id" => "2h9z2KeKZ2NV9ra",
            "trandata" => $transport,
            "responseURL" => "http://192.168.1.19/FlutterWave/public/api/rj/success",
            "errorURL" => "http://192.168.1.19/FlutterWave/public/api/rj/fail",

        ];

        $url = "https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm";

        $fields = array($fields);
        $response = $this->Curl($url, $fields);

        $res['payment_id'] = strstr($response['0']['result'], ':', true);
        $res['payment_page'] = strstr($response['0']['result'], 'h') . "?PaymentID" . "=" . $res['payment_id'];
        $res['confirm_page'] = 'http://192.168.1.14/FlutterWave/public/api/monri/callback';
        $res['listen_page'] = 'http://192.168.1.19/FlutterWave/public/api/rj/success';

        return $res;
    }

    public function success(Request $request)
    {
        return 1;
    }
    public function confirm($id)
    {
        $data = [
            "amt" => "1000.00",
            "action" => "8",
            "id" => "2h9z2KeKZ2NV9ra",
            "password" => '$$SBR8fmAz6$0e3',
            "currencyCode" => "682",
            "trackId" => "7888847",
            "udf5" => "PaymentID",
            "transId" => (string) $id,

        ];
        $data = array($data);
        $key = '10637214941710637214941710637214';

        $str = Json::encode($data);

        $transport = $this->encryptAES($str, $key);

        $fields = [
            "id" => "2h9z2KeKZ2NV9ra",
            "tranid" => (string) $id,
            "trandata" => $transport,
            "errorURL" => "http://192.168.1.19/FlutterWave/public/api/rj/fail",
            "responseURL" => "http://192.168.1.19/FlutterWave/public/api/rj/success",
        ];

        $url = "https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm";

        $fields = array($fields);
        return $response = $this->Curl($url, $fields);
    }
    public function encryptAES($str, $key)
    {
        $str = urlencode($str);

        $str = $this->pkcs5_pad($str);

        $ivlen = openssl_cipher_iv_length($cipher = "aes-256-cbc");

        $iv = "PGKEYENCDECIVSPC";

        $encrypted = openssl_encrypt($str, "aes-256-cbc", $key, OPENSSL_ZERO_PADDING, $iv);

        $encrypted = base64_decode($encrypted);

        $encrypted = unpack('C*', ($encrypted));

        $encrypted = $this->byteArray2Hex($encrypted);

        return $encrypted;
    }

    public function byteArray2Hex($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }

    public function pkcs5_pad($text)
    {
        $blocksize = 16;
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
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
            "Content-Type: application/json",

        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

}
