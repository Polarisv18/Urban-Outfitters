<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        .register-container h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .register-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            padding: 10px;
            font-size: 1em;
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        .register-container input[type="submit"] {
            padding: 10px;
            background-color: #000;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
            border-radius: 4px;
        }
        .register-container input[type="submit"]:hover {
            background-color: #444;
        }
        .register-container p {
            margin-top: 15px;
            font-size: 1em;
        }
        .register-container a {
            color: #000;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>Create an Account</h1>
        <form action="register-process.php" method="POST">
            <input type="text" name="first-name" placeholder="First Name" required>
            <input type="text" name="last-name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>
