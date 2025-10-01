<?php

class Payment
{

    private $zarinpalMerchantID = zarinpalMerchantID;

    private $CallbackURL = callbackURL;

    function __construct()
    {
        require('public/lib/nuSoap/nusoap.php');
    }

    function zarinpalRequest($Amount, $Description, $Email, $Mobile,$callback)
    {

        $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';



        $result = $client->call('PaymentRequest', [
            [
                'MerchantID' => $this->zarinpalMerchantID,
                'Amount' => $Amount,
                'Description' => $Description,
                'Email' => $Email,
                'Mobile' => $Mobile,
                'CallbackURL' => $callback,
            ],
        ]);

        $Authority = '';
        if ($result['Status'] == 100) {
            $Authority = $result['Authority'];
        }
        return ['Status' => $result['Status'], 'Authority' => $Authority];


    }


    function zarinpalVerify($Amount, $Authority)
    {
        $Error = '';
        $RefID = '';

        $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';

        $result = $client->call('PaymentVerification', [
            [
                'MerchantID' => $this->zarinpalMerchantID,
                'Authority' => $Authority,
                'Amount' => $Amount,
            ],
        ]);



        $Status = $result['Status'];

        if ($Status != 100) {
            $Error = "خطا در انجام عملیات :((";
        }
        if ($Status == 100) {
            $RefID = $result['RefID'];
        }
        return ['Status' => $Status, 'Error' => $Error, 'RefID' => $RefID];
    }


}

















