<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=login_required');
    exit;
}

// Check if mannequin items are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mannequin_items'])) {
    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Decode and process each item
    foreach ($_POST['mannequin_items'] as $encodedItem) {
        // Decode the JSON-encoded item
        $item = json_decode($encodedItem, true);

        // Validate item data
        if (isset($item['id'], $item['name'], $item['price'])) {
            $item_id = filter_var($item['id'], FILTER_VALIDATE_INT);
            $item_name = htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
            $item_price = filter_var($item['price'], FILTER_VALIDATE_FLOAT);

            if ($item_id && $item_price !== false) {
                // Add or update the item in the cart
                if (!isset($_SESSION['cart'][$item_id])) {
                    $_SESSION['cart'][$item_id] = [
                        'name' => $item_name,
                        'price' => $item_price,
                        'quantity' => 1
                    ];
                } else {
                    // Increment quantity if item already exists
                    $_SESSION['cart'][$item_id]['quantity'] += 1;
                }
            }
        }
    }

    // Redirect to the shopping cart page
    header('Location: shoppingcart.php?message=items_added');
    exit;
}

// If invalid request
header('Location: mannequin.php?message=invalid_request');
exit;
?>
