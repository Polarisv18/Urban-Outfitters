<?php
session_start();

// Check if the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['quantity'])) {
    // Sanitize input
    $itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Validate inputs
    if ($itemId === false || $quantity === false || $quantity < 1) {
        $_SESSION['error'] = "Invalid input.";
        header("Location: shoppingcart.php");
        exit;
    }

    // Update the cart
    if (isset($_SESSION['cart'][$itemId])) {
        $_SESSION['cart'][$itemId]['quantity'] = $quantity;
        $_SESSION['success'] = "Item updated successfully.";
    } else {
        $_SESSION['error'] = "Item not found in cart.";
    }

    // Redirect back
    header("Location: shoppingcart.php");
    exit;
}

// If invalid request
$_SESSION['error'] = "Invalid request.";
header("Location: shoppingcart.php");
exit;
