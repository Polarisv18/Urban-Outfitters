<?php
session_start();
include 'db_config.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_logged_in = true;
$is_admin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin';

// Fetch user's orders from the database
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Fetch order items for each order
function fetchOrderItems($order_id, $conn) {
    $sql = "SELECT oi.*, i.name FROM order_items oi JOIN items i ON oi.item_id = i.id WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle cancel order request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);

    // Verify the order belongs to the logged-in user
    $verify_sql = "SELECT id FROM orders WHERE id = ? AND user_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param('ii', $order_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();

    if ($verify_result->num_rows > 0) {
        $update_sql = "UPDATE orders SET status = 'Cancelled' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $order_id);
        if ($update_stmt->execute()) {
            $success_message = "Order #$order_id has been successfully canceled.";
        } else {
            $error_message = "Failed to cancel the order. Please try again.";
        }
    } else {
        $error_message = "Invalid order ID or you do not have permission to cancel this order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Orders - Suburban Outfitters</title>
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
        .orders-container {
            width: 80%;
            margin-top: 20px;
        }
        .order {
            border: 2px solid #000;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
        }
        .order-items {
            margin-top: 10px;
        }
        .order-items ul {
            list-style-type: disc;
            margin-left: 20px;
        }
        .order-status {
            margin-top: 10px;
        }
        .order-actions {
            margin-top: 10px;
        }
        .order-actions button {
            cursor: pointer;
            border: 2px solid #000;
            background-color: #000;
            color: #fff;
            padding: 5px 10px;
            font-size: 1em;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .order-actions button:hover {
            background-color: #fff;
            color: #000;
        }
        .message {
            margin: 10px 0;
            color: green;
        }
        .error {
            margin: 10px 0;
            color: red;
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

    <div class="orders-container">
        <h2>Manage My Orders</h2>

        <?php if (isset($success_message)): ?>
            <p class="message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <div class="order-header">
                        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                        <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
                    </div>
                    <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address'], ENT_QUOTES); ?></p>
                    <div class="order-items">
                        <p><strong>Items in this order:</strong></p>
                        <ul>
                            <?php $items = fetchOrderItems($order['id'], $conn); ?>
                            <?php foreach ($items as $item): ?>
                                <li><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?> - Quantity: <?php echo $item['quantity']; ?>, Price: $<?php echo number_format($item['price'], 2); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="order-status">
                        <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                    </div>
                    <?php if ($order['status'] !== 'Cancelled'): ?>
                        <div class="order-actions">
                            <form method="POST" action="">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="cancel_order">Cancel Order</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no orders.</p>
        <?php endif; ?>
    </div>
</body>
</html>
