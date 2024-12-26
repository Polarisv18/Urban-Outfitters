<?php
session_start();

// Check if the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ensure the POST request has the necessary data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Sanitize the item ID
    $itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($itemId !== false) {
        // Search and remove the item from the cart
        unset($_SESSION['cart'][$itemId]);
        $_SESSION['success'] = "Item removed successfully.";
    } else {
        $_SESSION['error'] = "Invalid item ID.";
    }

    // Redirect back to the shopping cart
    header("Location: shoppingcart.php");
    exit;
}

// If invalid request
header("Location: shoppingcart.php");
exit;
