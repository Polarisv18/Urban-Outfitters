<?php
session_start();
include 'db_config.php';

$username = $password = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Both fields are required!";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if (strtolower($user['role']) == 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: catalog.php');
                }
                exit;
            } else {
                $error = "Invalid credentials!";
            }
        } else {
            $error = "Invalid credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('item_images/homepage.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        form {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            width: 95%;
            padding: 10px;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: #333;
            color: #fff;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .btn-group {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .btn-group a {
            padding: 10px 20px;
            background: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }
        .btn-group a:hover {
            background: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Welcome Back!</h2>
        <p>Log in to continue</p>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <div class="btn-group">
            <a href="signup.php">Sign Up</a>
            <a href="catalog.php">Browse Items</a>
        </div>
    </form>
</body>
</html>
