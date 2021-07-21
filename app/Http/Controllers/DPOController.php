<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DPOController extends Controller
{
    public function token(Request $request)
    {

        $endpoint = 'https://secure.3gdirectpay.com/API/v6/';

        $PaymentURL = 'https://secure.3gdirectpay.com/payv2.php?ID=';
        $companytoken = '9F416C11-127B-4DE2-AC7F-D5710E4C5E0A';

        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
        <CompanyToken>' . $companytoken . '</CompanyToken>
        <Request>createToken</Request>
        <Transaction>
        <PaymentAmount>' . $request->amount . '</PaymentAmount>
        <PaymentCurrency>USD</PaymentCurrency>
        <CompanyRef>49FKEOA</CompanyRef>
        <RedirectURL>http://192.168.1.12/Flutter/public/api/dpo/success</RedirectURL>
        <BackURL>http://www.domain.com/backurl.php </BackURL>
        <CompanyRefUnique>0</CompanyRefUnique>
        <PTL>5</PTL>
        </Transaction>
        <Services>
          <Service>
            <ServiceType>5525</ServiceType>
            <ServiceDescription>Flight from Nairobi to Diani</ServiceDescription>
            <ServiceDate>2013/12/20 19:00</ServiceDate>
          </Service>
        </Services>
        </API3G>';

        $result = $this->Curl($endpoint, $xml);
        $xml = simplexml_load_string($result);

        $res['payment_id'] = (string) $xml->TransToken;
        $res['payment_page'] = $PaymentURL . (string) $xml->TransToken;
        $res['confirm_page'] = 'http://192.168.1.12/Flutter/public/api/dpo/verify';
        $res['listen_page'] = 'http://192.168.1.12/Flutter/public/api/dpo/success';

        return $res;

    }
    public function success(Request $request)
    {
        return $request->all();
    }

    public function verify()
    {
        $endpoint = 'https://secure.3gdirectpay.com/API/v6/';
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
          <CompanyToken>9F416C11-127B-4DE2-AC7F-D5710E4C5E0A</CompanyToken>
          <Request>verifyToken</Request>
          <TransactionToken>66BF4ECD-A3D4-4E3F-A27A-723004F06226</TransactionToken>
        </API3G>';

        return $result = $this->Curl($endpoint, $xml);
    }
    public function Curl($url, $xml)
    {

        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($xml),
            "Connection: close",
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        return $data;
        if (curl_errno($ch)) {
            print curl_error($ch);
        } else {
            curl_close($ch);
        }

    }
}
