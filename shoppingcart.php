<?php
session_start();
include 'db_config.php'; // Include database connection

// Check if the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch item details from the database to ensure up-to-date data
function fetchCartItems($cart, $conn) {
    $cartItems = [];
    foreach ($cart as $itemId => $itemDetails) {
        $quantity = $itemDetails['quantity'];

        // Query the database for the item details
        $sql = "SELECT * FROM items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $item['quantity'] = $quantity; // Add quantity from the session
            $cartItems[$itemId] = $item; // Add the item to the cartItems array
        }
    }
    return $cartItems;
}

$cart = $_SESSION['cart'];
$cartItems = fetchCartItems($cart, $conn);

// Calculate totals
function calculateTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $tax = $total * 0.08; // 8% tax rate
    return [
        'subtotal' => $total,
        'tax' => $tax,
        'total' => $total + $tax,
    ];
}

$totals = calculateTotal($cartItems);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            margin: 20px;
            background-color: #fff;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px;
            border-bottom: 2px solid #000;
        }
        .header .logo {
            font-size: 2.5em;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            color: inherit;
        }
        .nav {
            margin-top: 10px;
            display: flex;
            gap: 20px;
        }
        .nav a {
            font-size: 1.2em;
            text-decoration: none;
            color: #000;
            border: 2px solid #000;
            padding: 5px 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .nav a:hover {
            background-color: #000;
            color: #fff;
        }
        .cart-items {
            width: 80%;
            margin-top: 20px;
        }
        .cart-item {
            border: 2px solid #000;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item-details {
            flex: 2;
        }
        .cart-item h2 {
            font-size: 1.5em;
            margin: 0 0 10px;
        }
        .cart-item p {
            font-size: 1em;
            margin: 5px 0;
        }
        .cart-item input, .cart-item button {
            margin: 5px 0;
            padding: 5px;
            font-size: 1em;
        }
        .cart-item button {
            cursor: pointer;
            border: 2px solid #000;
            background-color: transparent;
            color: #000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .cart-item button:hover {
            background-color: #000;
            color: #fff;
        }
        .total {
            margin-top: 20px;
            font-size: 1.5em;
            text-align: right;
            width: 80%;
        }
        .checkout-button, .add-more-items {
            margin-top: 20px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            border: 2px solid #000;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .checkout-button:hover, .add-more-items:hover {
            background-color: #fff;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="homepage.php" class="logo">Suburban Outfitters</a>
        <div class="nav">
            <a href="catalog.php">Browse Items</a>
            <a href="mannequin.php">Virtual Mannequin</a>
            <a href="shoppingcart.php">Cart</a>
            <a href="manage_account.php">Manage Account</a>
            <a href="manage_user_orders.php">Manage Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="cart-items">
        <?php if (count($cartItems) > 0): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-details">
                        <h2><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></h2>
                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <p>Quantity:</p>
                        <form method="POST" action="update_cart.php">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" action="remove_from_cart.php">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your cart is empty. Start shopping!</p>
        <?php endif; ?>
    </div>
    <div class="total">
        <p>Subtotal: $<?php echo number_format($totals['subtotal'], 2); ?></p>
        <p>Tax (8%): $<?php echo number_format($totals['tax'], 2); ?></p>
        <p>Total: $<?php echo number_format($totals['total'], 2); ?></p>
    </div>
    <?php if (count($cartItems) > 0): ?>
        <div style="display: flex; justify-content: space-between; width: 80%;">
            <a class="add-more-items" href="catalog.php">Add More Items</a>
            <form method="POST" action="checkout.php" style="display:inline;">
                <button class="checkout-button" type="submit">Proceed to Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
