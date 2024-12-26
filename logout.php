<?php
session_start();

// Destroy the session
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px;
            border-bottom: 2px solid #000;
        }
        .logo {
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
        .logout-container {
            width: 100%;
            max-width: 600px;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logout-container h2 {
            margin-bottom: 20px;
        }
        .logout-container p {
            margin-bottom: 20px;
            font-size: 1.2em;
        }
        .logout-container a {
            padding: 10px 20px;
            font-size: 1.1em;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
        }
        .logout-container a:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <a href="homepage.php" class="logo">Suburban Outfitters</a>
        <div class="nav">
            <a href="catalog.php">Browse our items</a>
            <a href="login.php">Log In</a>
        </div>
    </div>

    <!-- Logout Section -->
    <div class="logout-container">
        <h2>You have been logged out</h2>
        <p>Thank you for visiting. You can now return to the homepage or log in again.</p>
        <a href="homepage.php">Go to Homepage</a>
    </div>

</body>
</html>
