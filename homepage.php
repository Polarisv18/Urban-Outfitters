<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore Suburban Outfitters, your destination for high-fashion outfits tailored to perfection.">
    <meta name="keywords" content="Fashion, Clothing, Suburban Outfitters, High-Fashion">
    <title>Suburban Outfitters</title>
    <link rel="icon" href="path/to/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('item_images/homepage.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .container {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            font-size: clamp(2em, 4vw, 3em);
            margin: 0;
        }

        p {
            font-size: clamp(1em, 2vw, 1.5em);
            margin: 0;
        }

        .buttons {
            margin-top: 20px;
        }

        .buttons a {
            font-size: 1em;
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
            border: 2px solid #fff;
            background-color: transparent;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #fff;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Suburban Outfitters</h1>
        <p>where your dream outfits are tailored</p>
        <div class="buttons">
            <a href="login.php" aria-label="Login to your account">Login</a>
            <a href="signup.php" aria-label="Create a new account">Sign Up</a>
            <a href="catalog.php" aria-label="Browse our clothing catalog">Browse our Catalog</a>
        </div>
    </div>
</body>
</html>
