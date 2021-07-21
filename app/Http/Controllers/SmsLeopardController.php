<?php

namespace App\Http\Controllers;

use App\Traits\reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsLeopardController extends Controller
{
    use reply;
    protected $account_id = 'nzMpA5pj4ou8pa9xW310';
    protected $account_secret = 'Ajkk6eJIIsHHRrcNVnfMlneTeczMoSU0fmw5CsbR';
    public function send_message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => "required",
            'recipents' => "required|array",
            "recipents.*.number" => 'required|integer',

        ]);
        if ($validator->errors()->all()) {return $this->failed($validator->errors()->first());}
        $url = 'https://api.smsleopard.com/v1/sms/send';
        // encoded header used in curl
        $auth = base64_encode($this->account_id . ':' . $this->account_secret);
        $fields = array(
            'source' => 'SMSLEOPARD',
            'message' => $request->message,
            'destination' => $request->recipents,

        );

        $response = $this->Curl($url, $fields, $auth);
        if ($response['success'] == 'true') {
            return $this->success("Message sent Successfully", "");
        } else {
            return $this->failed($response);
        }
    }
    public function Curl($url, $fields, $auth)
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
            "Authorization:Basic $auth",
        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

    public function s1end_message(Request $request)
    {
        $url = 'http://2factor.in/API/V1/98a9b00f-7b3d-11eb-a9bc-0200cd936042/ADDON_SERVICES/SEND/TSMS';

        $fields = array(
            'From' => 'TFCTOR',
            'To' => '919501360632',
            'Msg' => 'hello first message',

        );

        return $response = $this->Curll($url, $fields);

    }

    public function Curll($url, $fields)
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

        ));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $response;
    }

    public function sendMessage()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/v5/flow",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"flow_id\":\"6050b140dc1023147c1ab8db\",\"sender\":\"STHREE\",\"recipients\":[{\"mobiles\":\"919501360632\",\"VAR1\":\"12251\"}]}",
            CURLOPT_HTTPHEADER => array(
                "authkey: 279641AGbIqhqxeC5cf63f11",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }
}
