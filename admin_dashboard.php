<?php
session_start();

// Restrict access to admin users only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('item_images/homepage.jpg'); /* Background image */
            background-size: cover;
            background-position: center;
            color: #fff; /* White text for contrast */
        }
        .container {
            background: rgba(0, 0, 0, 0.8); /* Semi-transparent black background */
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .buttons a {
            text-decoration: none;
            font-size: 1em;
            padding: 10px;
            border-radius: 5px;
            color: #000;
            background-color: #fff;
            text-align: center;
            transition: all 0.3s ease;
        }
        .buttons a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, Admin!</h1>
        <p>What would you like to do today?</p>
        <div class="buttons">
            <a href="admin_dashboard.php">Back to Dashboard</a>
                <a href="catalog.php">Browse Items</a>
                <a href="admin_catalog.php">Manage Catalog</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="manage_orders.php">Manage Orders</a>
                <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
