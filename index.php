<?php
// Include Midtrans SDK
// Require Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Load the .env file manually (if needed)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $value = trim($value);
        if (preg_match('/^["\'].*["\']$/', $value)) {
            $value = substr($value, 1, -1);
        }
        putenv("$key=$value");
    }
}

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = getenv("MIDTRANS_SERVER_KEY");

// Set to Development/Sandbox mode or Production mode
\Midtrans\Config::$isProduction = getenv("MIDTRANS_IS_PRODUCTION"); // Change to `true` for production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Example transaction details
$transactionDetails = array(
    'order_id' => uniqid(), // Unique order ID
    'gross_amount' => 100000, // Total amount (e.g., 100000 = Rp 100,000)
);

// Item details
$itemDetails = array(
    array(
        'id' => 'item1',
        'price' => 100000,
        'quantity' => 1,
        'name' => "Sample Item"
    )
);

// Customer details
$customerDetails = array(
    'first_name' => "John",
    'last_name' => "Doe",
    'email' => "john.doe@example.com",
    'phone' => "081234567890",
    'billing_address' => array(
        'first_name' => "John",
        'last_name' => "Doe",
        'address' => "Jl. Kebon Jeruk",
        'city' => "Jakarta",
        'postal_code' => "12345",
        'phone' => "081234567890",
        'country_code' => 'IDN'
    ),
    'shipping_address' => array(
        'first_name' => "John",
        'last_name' => "Doe",
        'address' => "Jl. Kebon Jeruk",
        'city' => "Jakarta",
        'postal_code' => "12345",
        'phone' => "081234567890",
        'country_code' => 'IDN'
    )
);

// Transaction payload
$transaction = array(
    'transaction_details' => $transactionDetails,
    'item_details' => $itemDetails,
    'customer_details' => $customerDetails,
);

try {
    // Generate Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);

    // Return the token to frontend or use it in HTML form
    echo "Snap Token: " . $snapToken . "<br>";
    echo "<button id='pay-button'>Pay Now</button>";
    echo "
    <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='" . getenv("MIDTRANS_CLIENT_KEY") . "'></script>
    <script type='text/javascript'>
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            snap.pay('$snapToken');
        });
    </script>
    ";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
