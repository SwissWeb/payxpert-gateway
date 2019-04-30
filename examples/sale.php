<?php
require_once (dirname(__FILE__) . "/configuration.php");
require_once '../GatewayClient.php';

$client = new GatewayClient($gatewayURL , $originatorID, $password);


    $transaction = $client->newTransaction('CCSale');
    $transaction->setTransactionInformation(200, $currency,  '123456', '10.10.254.10');
    $transaction->setCardInformation('4111111111111111', '000', 'John Smith', '10', '2024');
    $transaction->setShopperInformation('John Smith', '123 Some Street', 'WC1A1AA', 'London', 'NA', 'GB', 'NA', 'test@mail.com');
    $transaction->setOrder(15000, 'NT94498', null, 'A nice set of tableware');
    $transaction->setAffiliate(123, 'test');
    $response = $transaction->send();

    var_dump($response);