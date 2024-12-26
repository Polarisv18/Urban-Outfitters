<?php
session_start();
include 'db_config.php';

// Initialize variables
$error = $success = "";
$username = $email = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 6 characters, include an uppercase letter and a number.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert the new user
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sss', $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = "Account created successfully! Please log in.";
                header('Location: login.php');
                exit;
            } else {
                $error = "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES);
            }
            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare the statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create your account at Suburban Outfitters to access tailored high-fashion clothing.">
    <meta name="keywords" content="Fashion, Clothing, Suburban Outfitters, High-Fashion, Sign Up">
    <title>Sign Up - Suburban Outfitters</title>
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
            padding: 30px;
            border-radius: 10px;
        }
        h1 {
            font-size: clamp(2em, 4vw, 3em);
            margin: 0 0 20px;
        }
        .form-container {
            margin-top: 20px;
        }
        input, button {
            font-size: clamp(1em, 1.2vw, 1.5em);
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #fff;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
        }
        input::placeholder {
            color: #999;
        }
        button {
            background-color: #fff;
            color: #000;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        button:hover {
            background-color: #000;
            color: #fff;
        }
        .error, .success {
            font-size: 1em;
            margin-bottom: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        a {
            font-size: clamp(1em, 1.2vw, 1.5em);
            color: #fff;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Your Account</h1>
        <p>Sign up to access personalized high-fashion clothing</p>
        <div class="form-container">
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
            <?php elseif (!empty($success)): ?>
                <p class="success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
