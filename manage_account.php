<?php
session_start();
include 'db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = $success = "";

// Fetch user data
$sql = "SELECT username, first_name, last_name, email, phone_number, shipping_address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Set default values for fields
    $user['first_name'] = $user['first_name'] ?? '';
    $user['last_name'] = $user['last_name'] ?? '';
    $user['phone_number'] = $user['phone_number'] ?? '';
    $user['shipping_address'] = $user['shipping_address'] ?? '';
} else {
    $error = "User not found.";
}

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone_number = htmlspecialchars(trim($_POST['phone_number']), ENT_QUOTES, 'UTF-8');
    $shipping_address = htmlspecialchars(trim($_POST['shipping_address']), ENT_QUOTES, 'UTF-8');

    if (empty($username) || empty($email)) {
        $error = "Username and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $sql = "UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ?, phone_number = ?, shipping_address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssi', $username, $first_name, $last_name, $email, $phone_number, $shipping_address, $user_id);

        if ($stmt->execute()) {
            $success = "Your information has been updated successfully.";
        } else {
            $error = "Failed to update your information.";
        }
    }
}

// Handle Password Reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Both password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $hashed_password, $user_id);

        if ($stmt->execute()) {
            $success = "Your password has been reset successfully.";
        } else {
            $error = "Failed to reset your password.";
        }
    }
}

// Handle Account Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        session_destroy();
        header('Location: signup.php');
        exit;
    } else {
        $error = "Failed to delete your account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account - Suburban Outfitters</title>
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
        .form-container {
            width: 60%;
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 2px solid #000;
            border-radius: 10px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin: 10px 0 5px;
            display: block;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1em;
            border: 2px solid #000;
            border-radius: 5px;
        }
        textarea {
            resize: none;
            height: 80px;
        }
        button {
            background-color: #000;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        button:hover {
            background-color: #fff;
            color: #000;
        }
        .error, .success {
            text-align: center;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .create-account {
            margin-top: 20px;
            text-align: center;
        }
        .create-account a {
            font-size: 1.2em;
            color: #000;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        .create-account a:hover {
            color: #000;
        }
    </style>
    <script>
        function confirmDeletion() {
            return confirm("\u26A0\uFE0F WARNING: Are you sure you want to delete your account? This action is irreversible.");
        }
    </script>
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

    <div class="form-container">
        <h2>Manage Your Account</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <h3>Update Information</h3>
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="shipping_address">Shipping Address</label>
            <textarea id="shipping_address" name="shipping_address"><?php echo htmlspecialchars($user['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <button type="submit" name="update_info">Update Information</button>
        </form>

        <form method="POST" action="">
            <h3>Reset Password</h3>
            <label for="new_password">New Password *</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirm New Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>

        <form method="POST" action="" onsubmit="return confirmDeletion();">
            <h3>Delete Account</h3>
            <button type="submit" name="delete_account" style="background-color: red; color: white;">Delete Account</button>
        </form>

        <div class="create-account">
            <p>Want to create a new account?</p>
            <a href="signup.php">Sign Up Now</a>
        </div>
    </div>
</body>
</html>
