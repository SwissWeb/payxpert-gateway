<?php
session_start();

require_once (dirname(__FILE__) . "/configuration.php");
require_once '../GatewayClient.php';

$TermUrl = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];

// Callback of the 3DS authentication
if (isset($_REQUEST['PaRes'])) {
  $transactionID = $_SESSION['transactionID'];
  $orderID = $_SESSION['orderID'];
  $PaRes = $_REQUEST['PaRes'];

  $client = new GatewayClient($gatewayURL, $originatorID, $password);

  $transaction = $client->newTransaction('CCSale');
  $transaction->setTransactionInformation(10000, $currency, $orderID, '10.10.254.10');
  $transaction->setCardInformation('4111111111111111', '000', 'John Smith', '10', '2024');
  $transaction->setShopperInformation('John Smith', '123 Some Street', 'WC1A1AA', 'London', 'NA', 'GB', 'NA', 'test@mail.com');
  $transaction->set3DSecurePaRes($PaRes);

  $response = $transaction->send();
  echo '<br /><h1 style="font-size:15px;">Sale</h2>' . "<br />\n";

  if ('000' === $response->errorCode) {
    $transactionID = $response->transactionID;
  } else {
    echo "Error {$response->errorCode} with message {$response->errorMessage}";
  }
  print_r($response);

  echo '</body></html>';
} else if (!isset($_REQUEST['card'])) {
  ?>
<html>
<body>
 <a href="?card=4111111111111111">Run 3Dsecure Enrolled Card Test transaction</a>
 <br />
 <a href="?card=4012888888881881">Run 3Dsecure Non-Enrolled Card Test transaction</a>
</body>
</html>

<?php
  exit();
} else {

  $orderID = uniqid();

  $client = new GatewayClient($gatewayURL, $originatorID, $password);

  // Run a 3DSCheck, this will tell if the card is 3DS enrolled (000) or not
  // (650)
  $transaction = $client->newTransaction('3DSCheck');
  $transaction->setTransactionInformation(10000, $currency, $orderID, '10.10.254.10');
  $transaction->set3DSecureCardInformation($_REQUEST['card'], '10', '2024', 'test@mail.com');

  $response = $transaction->send();

  // Card is enrolled
  if ('000' === $response->errorCode) {
    $transactionID = $response->transactionID;
    $ACSUrl = $response->ACSUrl;
    $PaReq = $response->PaReq;
    $_SESSION['transactionID'] = $transactionID;
    $_SESSION['orderID'] = $orderID;
    $_SESSION['card'] = $_REQUEST['card'];
    // CVV and EXP must be kept as well: /!\ CVV must never be stored in clear
    // text.
    // We redirect the customer towards the ACS
    ?>
<html>
<body>
 <form name="3DS-ACS-Redirect" action="<?php echo $ACSUrl ?>" method="POST">
  <input type="hidden" name="PaReq" value="<?php echo $PaReq ?>">
  <input type="hidden" name="TermUrl" value="<?php echo $TermUrl ?>">
  <input type="hidden" name="MD" value="<?php echo $orderID ?>">
  <input type="submit" value="Continue to Test ACS">
 </form>
</body>
</html>
<?php
  } else if ('650' === $response->errorCode) {
    // If code is 650, the card is not enrolled
    // If non enrolled cards are not desirable the process should stop here
    $transaction = $client->newTransaction('CCSale');
    $transaction->setTransactionInformation(10000, $currency, $orderID, '10.10.254.10');
    $transaction->setCardInformation($_REQUEST['card'], '000', 'John Smith', '10', '2024');
    $transaction->setShopperInformation('John Smith', '123 Some Street', 'WC1A1AA', 'London', 'NA', 'GB', 'NA', 'test@mail.com');

    echo '<br /><h1 style="font-size:15px;">3DSCheck</h2>' . "<br />\n";
    print_r($response);

    $response = $transaction->send();
    echo '<br /><h1 style="font-size:15px;">Sale</h2>' . "<br />\n";

    if ('000' === $response->errorCode) {
      $transactionID = $response->transactionID;
    } else {
      echo "Error {$response->errorCode} with message {$response->errorMessage}";
    }
    print_r($response);
  } else {
    echo "Error {$response->errorCode} with message {$response->errorMessage}";
  }
}
?>