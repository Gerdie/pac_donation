<?php
/**
 * Created by PhpStorm.
 * User: maria.moy
 * Date: 7/11/2016
 * Time: 9:44 AM
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

if(isset($_POST['other-amount']) && $_POST['amount']=="Other")
{
    $str_amount=$_POST['other-amount'];
    $amount= (int) $str_amount * 100;
} elseif ($_POST['amount']==5000){
    $amount= 5000 * 100;
} elseif ($_POST['amount']==2500){
    $amount= 2500 * 100;
} elseif ($_POST['amount']==1500){
    $amount= 1500 * 100;
} elseif ($_POST['amount']==500){
    $amount= 500 * 100;
} else {
    $amount=null;
}

$given_name=$_POST['given_name'];
$family_name=$_POST['family_name'];
$address_line_1=$_POST['address_line_1'];
$address_line_2=$_POST['address_line_2'];
$locality=$_POST['locality'];
$administrative_district_level_1=$_POST['administrative_district_level_1'];
$postal_code=$_POST['postal_code'];
$company_name=$_POST['company_name'];
$chapter_name=$_POST['chapter_name'];
$note= "Title: " . $_POST['note'] . " & Chapter: " . $chapter_name . " & Donation Amount: " . $amount;
$email_address=$_POST['email_address'];

session_start();
$_SESSION['amount'] = $amount;
$_SESSION['address_line_1'] = $address_line_1;
$_SESSION['address_line_2'] = $address_line_1;
$_SESSION['locality'] = $locality;
$_SESSION['administrative_district_level_1'] = $administrative_district_level_1;
$_SESSION['postal_code'] = $postal_code;
$_SESSION['note2'] = $note2;

$customer_api = new \SquareConnect\Api\CustomerApi();

$customer_body = array (
    "given_name" => $given_name,
    "family_name" => $family_name,
    "address" => array (
        "address_line_1" => $address_line_1,
        "address_line_2" => $address_line_2,
        "locality" => $locality,
        "administrative_district_level_1" => $administrative_district_level_1,
        "postal_code" => $postal_code,
        "country" => "US"
    ),
    "company_name" => $company_name,
    "note" => $note,
    "email_address" => $email_address,
);

try {
    $result2 = $customer_api->createCustomer($access_token, $customer_body);
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
    <script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>
    <script type="text/javascript">
        var sqPaymentForm = new SqPaymentForm({
    //  not real application ID,
    applicationId: 'XXXXXXXXXX',
            inputClass: 'sq-input',
            cardNumber: {
        elementId: 'sq-card-number',
                placeholder: "0000 0000 0000 0000"
            },
            cvv: {
        elementId: 'sq-cvv',
                placeholder: 'CVV'
            },
            expirationDate: {
        elementId: 'sq-expiration-date',
                placeholder: 'MM/YY'
            },
            postalCode: {
        elementId: 'sq-postal-code',
                placeholder: 'Postal Code'
            },
            inputStyles: [
                // Because this object provides no value for mediaMaxWidth or mediaMinWidth,
                // these styles apply for screens of all sizes, unless overridden by another
                // input style below.
                {
                    fontSize: '14px',
                    padding: '3px'
                },
                // These styles are applied to inputs ONLY when the screen width is 400px
                // or smaller. Note that because it doesn't specify a value for padding,
                // the padding value in the previous object is preserved.
                {
                    mediaMaxWidth: '400px',
                    fontSize: '18px',
                }
            ],
            callbacks: {
        cardNonceResponseReceived: function(errors, nonce, cardData) {
            if (errors) {
                var errorDiv = document.getElementById('errors');
                errorDiv.innerHTML = "";
                errors.forEach(function(error) {
                    var p = document.createElement('p');
                    p.innerHTML = error.message;
                    errorDiv.appendChild(p);
                });
                    } else {
                // This alert is for debugging purposes only.
                //alert('Nonce received! ' + nonce + ' ' + JSON.stringify(cardData));
                // Assign the value of the nonce to a hidden form element
                var nonceField = document.getElementById('card-nonce');
                nonceField.value = nonce;
                // Submit the form
                document.getElementById('form').submit();
            }
        },
        unsupportedBrowserDetected: function() {
            // Alert the buyer that their browser is not supported
        }
    }
        });
        function submitButtonClick(event) {
            event.preventDefault();
            sqPaymentForm.requestCardNonce();
        }
        function chooseOther() {
            document.getElementById("enterAmount").style.display = "block";
            document.getElementById("other-amount").style.display = "block";
            document.getElementById("custom2").style.display = "block";
        }
        function choosePres() {
            event.preventDefault();
            document.getElementById("senatorial").style.display = "none";
            document.getElementById("congressional").style.display = "none";
            document.getElementById("grassroots").style.display = "none";
            document.getElementById("custom").style.display = "none";
        }
        function chooseSen() {
            event.preventDefault();
            document.getElementById("presidential").style.display = "none";
            document.getElementById("congressional").style.display = "none";
            document.getElementById("grassroots").style.display = "none";
            document.getElementById("custom").style.display = "none";
        }
        function chooseCon() {
            event.preventDefault();
            document.getElementById("presidential").style.display = "none";
            document.getElementById("senatorial").style.display = "none";
            document.getElementById("grassroots").style.display = "none";
            document.getElementById("custom").style.display = "none";
        }
        function chooseGrass() {
            event.preventDefault();
            document.getElementById("presidential").style.display = "none";
            document.getElementById("senatorial").style.display = "none";
            document.getElementById("congressional").style.display = "none";
            document.getElementById("custom").style.display = "none";
        }
    </script>
</head>
<body>
    <div class="background">
    <div class="container">
        <div class="container2">
            <a href="http://www.agc.org"><img class="logo" src="/pac/AGCPAClogo.jpg"></a>

            <div class="form-half">
                <h1>Donate to the AGC PAC</h1>
                <form id="form" novalidate action="/pac/process-card2.php" method="post">
                    <label>Billing Information</label>
                    <input class="sq-input" type="text" name="note2" id="note2" placeholder="Name on Credit Card" required>
                    <label>Credit Card</label>
                        <div id="sq-card-number"></div>
                    <label>CVV</label>
                        <div id="sq-cvv"></div>
                    <label>Expiration Date</label>
                        <div id="sq-expiration-date"></div>
                    <label>Postal Code</label>
                        <div id="sq-postal-code"></div>
                        <input type="hidden" id="card-nonce" name="nonce">
                        <input type="submit" onclick="submitButtonClick(event)" id="card-nonce" value="Donate">
                </form>
                <div id="errors"></div>
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
</html>