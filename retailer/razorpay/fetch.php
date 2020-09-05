<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);



$razorpayOrder = $api->order->fetch($_GET['oid']);

//$json = json_encode((array)$razorpayOrder);
echo $razorpayOrder->amount; echo "<br/>";
echo $razorpayOrder->id; echo "<br/>";
echo $razorpayOrder->receipt; echo "<br/>";
print_r($razorpayOrder);
//require("checkout/{$checkout}.php");
