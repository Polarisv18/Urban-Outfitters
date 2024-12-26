<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Suburban Outfitters</title>
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
        .reset-password-container {
            width: 100%;
            max-width: 600px;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .reset-password-container h2 {
            margin-bottom: 20px;
        }
        .reset-password-container label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }
        .reset-password-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }
        .reset-password-container button {
            padding: 10px 20px;
            font-size: 1.1em;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .reset-password-container button:hover {
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
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Reset Password Section -->
    <div class="reset-password-container">
        <h2>Reset Your Password</h2>
        <p>If you've forgotten your password, enter your email address below and we'll send you a link to reset it.</p>
        <form action="send-reset-link.php" method="POST">
            <div>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <button type="submit">Send Reset Link</button>
            </div>
        </form>
    </div>

</body>
</html>
