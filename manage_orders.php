<?php
session_start();
include 'db_config.php'; // Include database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Check if the user is an admin
$is_admin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin';

$user_id = $_SESSION['user_id'];

// Fetch orders and associated items based on role
if ($is_admin) {
    // Admins can view all orders
    $sql = "SELECT orders.id AS order_id, orders.user_id, users.first_name, orders.total_amount, orders.shipping_address, orders.status, orders.order_date FROM orders JOIN users ON orders.user_id = users.id";
} else {
    // Users can only view their own orders
    $sql = "SELECT orders.id AS order_id, orders.total_amount, orders.shipping_address, orders.status, orders.order_date FROM orders WHERE orders.user_id = ?";
}
$stmt = $conn->prepare($sql);
if (!$is_admin) {
    $stmt->bind_param('i', $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Fetch order items for all orders
$order_items = [];
foreach ($orders as $order) {
    $order_id = $order['order_id'];
    $item_sql = "SELECT items.name, order_items.quantity, order_items.price FROM order_items JOIN items ON order_items.item_id = items.id WHERE order_items.order_id = ?";
    $item_stmt = $conn->prepare($item_sql);
    $item_stmt->bind_param('i', $order_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    $order_items[$order_id] = $item_result->fetch_all(MYSQLI_ASSOC);
}

// Handle POST requests for updating or deleting orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_order']) && $is_admin) {
        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
    } elseif (isset($_POST['update_order'])) {
        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

        // Only admins can update any order
        // Users can only update their own orders
        if ($is_admin || (isset($_POST['user_id']) && filter_var($_POST['user_id'], FILTER_VALIDATE_INT) == $user_id)) {
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $status, $order_id);
            $stmt->execute();
            $_SESSION['status_updated'] = true;
        }
    }

    // Refresh page after actions
    header('Location: manage_orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Suburban Outfitters</title>
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
        .orders {
            width: 80%;
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
        button {
            padding: 10px;
            font-size: 1em;
            border: 2px solid #000;
            background-color: #000;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        button:hover {
            background-color: #fff;
            color: #000;
        }
        .order-items {
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .status-message {
            margin: 10px 0;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="homepage.php" class="logo">Suburban Outfitters</a>
        <div class="nav">
            <?php if ($is_admin): ?>
                <a href="admin_dashboard.php">Back to Dashboard</a>
                <a href="catalog.php">Browse Items</a>
                <a href="mannequin.php">Virtual Mannequin</a>
                <a href="admin_catalog.php">Manage Catalog</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="manage_orders.php">Manage Orders</a>
                <a href="logout.php">Logout</a>
            <?php elseif ($is_logged_in): ?>
                <a href="catalog.php">Browse Items</a>
                <a href="mannequin.php">Virtual Mannequin</a>
                <a href="shoppingcart.php">Cart</a>
                <a href="manage_account.php">Manage Account</a>
                <a href="manage_orders.php">Manage Orders</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['status_updated']) && $_SESSION['status_updated']): ?>
        <div class="status-message">Order status updated successfully!</div>
        <?php unset($_SESSION['status_updated']); ?>
    <?php endif; ?>

    <div class="orders">
        <h2>Manage Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <?php if ($is_admin): ?>
                        <th>User Name</th>
                    <?php endif; ?>
                    <th>Total Amount</th>
                    <th>Shipping Address</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php if ($is_admin): ?>
                            <td><?php echo htmlspecialchars($order['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php endif; ?>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <select name="status">
                                    <option value="Pending" <?php if ($order['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Shipped" <?php if ($order['status'] === 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if ($order['status'] === 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if ($order['status'] === 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                                               <button type="submit" name="update_order">Update</button>
                            </form>
                            <?php if ($is_admin): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" name="delete_order" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="order-items">
                        <td colspan="7">
                            <strong>Items in this order:</strong>
                            <ul>
                                <?php foreach ($order_items[$order['order_id']] as $item): ?>
                                    <li><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?> - Quantity: <?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?>, Price: $<?php echo number_format($item['price'], 2); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

