<?php
session_start();
include 'db_config.php'; // Include database connection

// Check if the cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='catalog.php'>Go shopping</a>.</p>";
    exit;
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Fetch user details for pre-filling checkout form
$sql = "SELECT first_name, email, shipping_address, phone_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Fetch item details dynamically from the database
function fetchCartDetails($cart, $conn) {
    $cartDetails = [];
    foreach ($cart as $itemId => $itemDetails) {
        $quantity = $itemDetails['quantity'];
        $sql = "SELECT id, name, price FROM items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $item['quantity'] = $quantity; // Include the quantity from the session
            $cartDetails[] = $item;
        }
    }
    return $cartDetails;
}

$cartDetails = fetchCartDetails($cart, $conn);

// Calculate totals
function calculateTotal($cartDetails) {
    $total = 0;
    foreach ($cartDetails as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

$total = calculateTotal($cartDetails);

// Handle the checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (empty($first_name) || empty($email) || empty($address) || empty($phone)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $error = "Invalid phone number!";
    } else {
        // Insert the order into the `orders` table
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ids', $user_id, $total, $address);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;

            foreach ($cartDetails as $item) {
                // Insert item into the order_items table
                $sql = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iiid', $order_id, $item['id'], $item['quantity'], $item['price']);
                if (!$stmt->execute()) {
                    $error = "Failed to record item in the order: " . $item['name'];
                    break;
                }

                // Update stock quantity in the items table
                $sql = "UPDATE items SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iii', $item['quantity'], $item['id'], $item['quantity']);
                if (!$stmt->execute()) {
                    $error = "Failed to update stock for item: " . $item['name'];
                    break;
                }
            }

            // If no errors, clear the cart and display a success message
            if (!isset($error)) {
                $_SESSION['cart'] = []; // Clear the cart
                $success = true;
            }
        } else {
            $error = "Failed to place the order.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Suburban Outfitters</title>
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
            justify-content: center;
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
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #000;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #fff;
            color: #000;
            border: 2px solid #000;
        }
        .cart-summary {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .success-message {
            text-align: center;
            padding: 20px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-top: 20px;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
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

    <div class="container">
        <?php if (isset($success) && $success): ?>
            <div class="success-message">
                <h2>Your order has been placed successfully!</h2>
                <p>Thank you for shopping with us!</p>
                <a href="catalog.php">Continue Shopping</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <h2>Checkout</h2>
                <?php if (isset($error)): ?>
                    <div class="error">Reminder: <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <label for="first_name">Name:</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your name" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                <label for="address">Shipping Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo htmlspecialchars($user_data['shipping_address'] ?? ''); ?>" required>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>"
required>
                <div class="cart-summary">
                    <h3>Your Order</h3>
                    <table>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        <?php foreach ($cartDetails as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th colspan="2">Total</th>
                            <th>$<?php echo number_format($total, 2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">Shipping: Free</th>
                        </tr>
                    </table>
                </div>
                <button type="submit">Confirm Checkout - Purge Day: Everything is Free</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
