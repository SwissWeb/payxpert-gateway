<?php

require_once (dirname(__FILE__) . "/configuration.php");
include('../GatewayClient.php');

$client = new GatewayClient($gatewayURL , $originatorID, $password);

$transaction = $client->newTransaction('ExportTransaction');
$transaction->setExportInterval(time() - 24*60*60, time());  //1 day window
$transaction->setExportTransactionOperation('sale');          // sale only
$transaction->setExportErrorCode('000');                 //Successful transaction only

$response = $transaction->send();

if ('000' === $response->errorCode) {
    $transactionList = $response->transactionList;
    print_r($transactionList);
} else {
    echo "Error {$response->errorCode} with message {$response->errorMessage}";
}


$transaction = $client->newTransaction('ExportTransaction');
$transaction->setExportInterval(time() - 24*60*60, time());  //1 day window
$transaction->setExportTransactionOperation('refund');    // refund only
$transaction->setExportErrorCode('000');                 //Successful transaction only

$response = $transaction->send();

if ('000' === $response->errorCode) {
    $transactionList = $response->transactionList;
    print_r($transactionList);
} else {
    echo "Error {$response->errorCode} with message {$response->errorMessage}";
}