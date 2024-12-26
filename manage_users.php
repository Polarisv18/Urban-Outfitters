<?php
session_start();
include 'db_config.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header('Location: login.php');
    exit;
}

// Initialize variables for user management
$edit_mode = false;
$id = $username = $first_name = $last_name = $email = $role = $phone_number = $shipping_address = '';

// Handle Create, Update, Edit, and Delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        // CREATE
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES);
        $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES);
        $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES);
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
        $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES);
        $phone_number = htmlspecialchars(trim($_POST['phone_number']), ENT_QUOTES);
        $shipping_address = htmlspecialchars(trim($_POST['shipping_address']), ENT_QUOTES);

        $sql = "INSERT INTO users (username, first_name, last_name, email, password, role, phone_number, shipping_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $username, $first_name, $last_name, $email, $password, $role, $phone_number, $shipping_address);

        if ($stmt->execute()) {
            echo "<script>alert('User added successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } elseif (isset($_POST['update'])) {
        // UPDATE
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES);
        $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES);
        $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES);
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES);
        $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES);
        $phone_number = htmlspecialchars(trim($_POST['phone_number']), ENT_QUOTES);
        $shipping_address = htmlspecialchars(trim($_POST['shipping_address']), ENT_QUOTES);

        $sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, role=?, phone_number=?, shipping_address=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssi', $username, $first_name, $last_name, $email, $role, $phone_number, $shipping_address, $id);

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } elseif (isset($_POST['delete'])) {
        // DELETE
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo "<script>alert('User deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } elseif (isset($_POST['edit'])) {
        // EDIT - Load user data into the form
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES);
        $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES);
        $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES);
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES);
        $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES);
        $phone_number = htmlspecialchars(trim($_POST['phone_number']), ENT_QUOTES);
        $shipping_address = htmlspecialchars(trim($_POST['shipping_address']), ENT_QUOTES);
        $edit_mode = true;
    } elseif (isset($_POST['cancel'])) {
        // CANCEL editing
        $edit_mode = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
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
            color: #000;
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
        .form-container, .table-container {
            width: 80%;
            margin: 20px 0;
        }
        input, textarea, button, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            font-size: 1em;
            border: 2px solid #000;
            border-radius: 5px;
        }
        button {
            background-color: transparent;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        button:hover {
            background-color: #000;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboard.php" class="logo">Suburban Outfitters</a>
        <div class="nav">
            <a href="admin_dashboard.php">Back to Dashboard</a>
            <a href="catalog.php">Browse Items</a>
            <a href="mannequin.php">Virtual Mannequin</a>
            <a href="admin_catalog.php">Manage Catalog</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_orders.php">Manage Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-container">
        <h2><?php echo $edit_mode ? "Edit User" : "Add New User"; ?></h2>
        <form method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username, ENT_QUOTES); ?>" required>
            <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($first_name, ENT_QUOTES); ?>">
            <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($last_name, ENT_QUOTES); ?>">
            <input type="email" placeholder="Email" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" required>
            <?php if (!$edit_mode): ?>
                <input type="password" name="password" placeholder="Password" required>
            <?php endif; ?>
            <select name="role" required>
                <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone_number, ENT_QUOTES); ?>">
            <input type="text" name="shipping_address" placeholder="Shipping Address" value="<?php echo htmlspecialchars($shipping_address, ENT_QUOTES); ?>">
            <?php if ($edit_mode): ?>
                <button type="submit" name="update">Update User</button>
                <button type="submit" name="cancel">Cancel</button>
            <?php else: ?>
                <button type="submit" name="create">Add User</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <h2>All Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['role'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['shipping_address'], ENT_QUOTES); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>">
                            <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($row['first_name'], ENT_QUOTES); ?>">
                            <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($row['last_name'], ENT_QUOTES); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>">
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['role'], ENT_QUOTES); ?>">
                            <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES); ?>">
                            <input type="hidden" name="shipping_address" value="<?php echo htmlspecialchars($row['shipping_address'], ENT_QUOTES); ?>">
                            <button type="submit" name="edit">Edit</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr><td colspan="9">No users found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php $conn->close(); ?>
</body>
</html>

