<?php
session_start();
include 'db_config.php'; // Include database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin');

// Ensure sessions exist for cart and mannequin items
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['mannequin_items'])) {
    $_SESSION['mannequin_items'] = [];
}

// Handle "Add to Cart" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id']);
    $item_name = $_POST['item_name'];
    $item_price = floatval($_POST['item_price']);

    // Check if the item already exists in the cart
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity']++; // Increment quantity
    } else {
        // Add new item to the cart
        $_SESSION['cart'][$item_id] = [
            'id' => $item_id,
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => 1,
        ];
    }

    $_SESSION['add_to_cart_message'] = "Item '$item_name' added to cart!";
    header('Location: catalog.php');
    exit;
}

// Handle "Style It" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style_it'])) {
    $item_id = intval($_POST['item_id']);
    $item_name = $_POST['item_name'];
    $item_price = floatval($_POST['item_price']);
    $item_image = $_POST['item_image'];

    // Add item to mannequin session
    $_SESSION['mannequin_items'][] = [
        'id' => $item_id,
        'name' => $item_name,
        'price' => $item_price,
        'image' => $item_image,
    ];

    header('Location: mannequin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog - Suburban Outfitters</title>
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
        .catalog {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            width: 80%;
        }
        .item {
            border: 2px solid #000;
            padding: 20px;
            text-align: center;
        }
        .item img {
            max-width: 100%;
        }
        .item h2 {
            font-size: 1.5em;
            margin: 10px 0;
        }
        .item p {
            margin: 10px 0;
        }
        .item button {
            margin: 10px 5px;
            padding: 10px 20px;
            cursor: pointer;
            border: 2px solid #000;
            background-color: transparent;
            color: #000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .item button:hover {
            background-color: #000;
            color: #fff;
        }
        .message {
            margin: 20px;
            padding: 10px;
            border: 2px solid green;
            color: green;
            background-color: #f9fff9;
            width: 80%;
            text-align: center;
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
                <a href="manage_user_orders.php">Manage Orders</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="homepage.php">Back to Home Page</a>
                <a href="login.php">Log In</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['add_to_cart_message'])): ?>
        <div class="message">
            <?php echo htmlspecialchars($_SESSION['add_to_cart_message'], ENT_QUOTES); unset($_SESSION['add_to_cart_message']); ?>
        </div>
    <?php endif; ?>

    <div class="catalog">
        <?php
        // Fetch items from the database
        $sql = "SELECT id, name, price, description, image FROM items";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='item'>";
                echo "<img src='item_images/" . htmlspecialchars($row['image'], ENT_QUOTES) . "' alt='" . htmlspecialchars($row['name'], ENT_QUOTES) . "'>";
                echo "<h2>" . htmlspecialchars($row['name'], ENT_QUOTES) . "</h2>";
                echo "<p>" . htmlspecialchars($row['description'], ENT_QUOTES) . "</p>";
                echo "<p>Price: $" . htmlspecialchars($row['price'], ENT_QUOTES) . "</p>";

                // Add to Cart button
                if ($is_logged_in) {
                    echo "<form method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['id'], ENT_QUOTES) . "'>";
                    echo "<input type='hidden' name='item_name' value='" . htmlspecialchars($row['name'], ENT_QUOTES) . "'>";
                    echo "<input type='hidden' name='item_price' value='" . htmlspecialchars($row['price'], ENT_QUOTES) . "'>";
                    echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
                    echo "</form>";

                    // Style It button
                    echo "<form method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['id'], ENT_QUOTES) . "'>";
                    echo "<input type='hidden' name='item_name' value='" . htmlspecialchars($row['name'], ENT_QUOTES) . "'>";
                    echo "<input type='hidden' name='item_price' value='" . htmlspecialchars($row['price'], ENT_QUOTES) . "'>";
                    echo "<input type='hidden' name='item_image' value='" . htmlspecialchars($row['image'], ENT_QUOTES) . "'>";
                    echo "<button type='submit' name='style_it'>Style It</button>";
                    echo "</form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No items found in the catalog.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
