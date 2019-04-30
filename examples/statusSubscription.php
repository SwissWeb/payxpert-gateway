<?php
require_once (dirname(__FILE__) . "/configuration.php");
require_once '../GatewayClient.php';

$client = new GatewayClient($gatewayURL , $originatorID, $password);

$subscriptionID = "your_subsctiption_ID";

$transaction = $client->newTransaction('StatusSubscription');
$transaction->setSubscriptionID($subscriptionID);
$response = $transaction->send();

if ('000' === $response->errorCode) {
    $subscription    = $response->subscription;
    $transactionList = $response->transactionList;
    var_dump($response);
} else {
   echo "Error {$response->errorCode} with message {$response->errorMessage}";
}
