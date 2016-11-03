<?php
/**
 * Created by PhpStorm.
 * User: maria.moy
 * Date: 7/5/2016
 * Time: 3:07 PM
 */

require '../../vendor/autoload.php';
# Not real credentials
$location_id = 'XXXXXXX';
# Not real access token 
$access_token = 'XXXXXXX';
# Helps ensure this code has been reached via form submission
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    error_log("Received a non-POST request");
    echo "Request not allowed";
    http_response_code(405);
    return;
}

# Fail if the card form didn't send a value for `nonce` to the server
$nonce = $_POST['nonce'];
if (is_null($nonce)) {
    echo "Invalid card data";
    http_response_code(422);
    return;
}

$note2=$_POST['note2'];

session_start();

$transaction_api = new \SquareConnect\Api\TransactionApi();
$request_body = array (
    "card_nonce" => $nonce,
    # Monetary amounts are specified in the smallest unit of the applicable currency.
    # This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
    "amount_money" => array (
        "amount" => $_SESSION['amount'],
        "currency" => "USD"
    ),
    "billing_address" => array (
        "address_line_1" => $_SESSION['address_line_1'],
        "address_line_2" => $_SESSION['address_line_2'],
        "administrative_district_level_1" => $_SESSION['administrative_district_level_1'],
        "locality" => $_SESSION['locality'],
        "postal_code" => $_SESSION['postal_code'],
        "country" => "US"
    ),
    # Every payment you process with the SDK must have a unique idempotency key.
    # If you're unsure whether a particular payment succeeded, you can reattempt
    # it with the same idempotency key without worrying about double charging
    # the buyer.
    "idempotency_key" => uniqid(),
    "note" => "Name on Credit Card: " . $note2,
);
# The SDK throws an exception if a Connect endpoint responds with anything besides
# a 200-level HTTP code. This block catches any exceptions that occur from the request.
try {
    $result = $transaction_api->charge($access_token, $location_id, $request_body);
    #echo "<pre>";
    #print_r($result);
    #echo "</pre>";
} catch (\SquareConnect\ApiException $e) {
    #echo "Caught exception!<br/>";
    #print_r("<strong>Response body:</strong><br/>");
    #echo "<pre>"; var_dump($e->getResponseBody()); echo "</pre>";
    #echo "<br/><strong>Response headers:</strong><br/>";
    #echo "<pre>"; var_dump($e->getResponseHeaders()); echo "</pre>";
}

?>
<head  profile="http://www.w3.org/2005/10/profile">
    <title>AGC PAC Donation Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="formstyles.css">
    <link rel="icon"
          type="image/png"
          href="http://www.iconmaterials.com/core/files/iconmaterials/uploads/images/Logos/AGC%20Logo.png">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="background">
    <div class="container">
        <div class="container2">
            <a href="www.agc.org"><img class="logo" src="/pac/AGCPAClogo.jpg"></a>
            <div class="form-half">
                <h1>Donate to the AGC PAC</h1>
                <p>Thank you! Your donation has been received. We appreciate your support.</p>
                <p>If you have any questions, please contact David Ashinoff at <a href="mailto:ashinoffd@agc.org">ashinoffd@agc.org</a>.</p>
                <img style="width:160px; display:block; margin:auto;" src="/pac/footer-logo.png">
                <p>Contributions to AGC PAC are not tax deductible for federal income tax purposes. Contributions to AGC PAC are for political purposes. All contributions to AGC PAC are voluntary. You may refuse to contribute without reprisal. The proposed contribution amounts are suggestions; you may choose to contribute more or less, or not at all. AGC will not favor or disadvantage anyone by reason of the amount of their contribution or their decision not to contribute. Federal law requires political committees to report the name, address, occupation and name of employer for each individual whose contributions aggregate in excess of $200 in a calendar year. Corporate donations are prohibited.</p>
            </div>
            <div class="sidebar">
                <h2>AGC PAC Benefits</h2>
                <p>All AGC PAC contributors will receive a subscription to The Advocate newsletter with "inside the beltway" news and a report at the end of each election cycle detailing our activities.</p>
                <h3>Presidential Trust ($5,000 annually)</h3>
                <p>AGC PAC's most generous supporters who contribute the maximum-allowed amount receive membership in the Presidential Trust. Members receive all the benefits listed below as well as a hotel welcome gift at national conferences.</p>
                <h3>Senatorial Council ($2,500 annually)</h3>
                <p>Senatorial Council members are committed to helping advance AGC's legislative agenda on Capitol Hill. Members receive the benefits listed below as well as preferred seating at national conference events and a semi-annual conference call with an elected official, politico or pundit.</p>
                <h3>Congressional Caucus ($1,500 annually)</h3>
                <p>Supporters in the Congressional Caucus are among our more politically active members. The benefits of giving at this level include those listed below as well as a donor club lapel pin and access to the Convention club room.</p>
                <h3>Grassroots Member ($500 annually)</h3>
                <p>Grassroots Members form the foundation of AGC PAC. Their support gives AGC a strong voice in Washington, DC. They may request candidates for AGC PAC support and participate in AGC PAC check presentations. As an added benefit, members receive an annual subscription to NationalJournal.com.</p>
            </div>
        </div>
    </div>
</div>
</body>
