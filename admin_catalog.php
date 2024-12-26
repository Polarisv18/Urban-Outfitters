<?php
session_start();
include 'db_config.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header('Location: login.php');
    exit;
}

// Initialize variables for catalog management
$edit_mode = false;
$id = $name = $description = $price = $quantity = '';

// Handle Create, Update, Edit, and Delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        // CREATE
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES);
        $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        $image_name = 'homepage.jpg'; // Default placeholder image

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image_name = basename($_FILES['image']['name']);
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_folder = 'item_images/' . $image_name;

            if (!move_uploaded_file($image_tmp, $image_folder)) {
                echo "<script>alert('Failed to upload image. Using placeholder image.');</script>";
                $image_name = 'homepage.jpg';
            }
        }

        $sql = "INSERT INTO items (name, description, price, stock_quantity, image) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdis', $name, $description, $price, $quantity, $image_name);

        if ($stmt->execute()) {
            echo "<script>alert('Item added successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['update'])) {
        // UPDATE
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES);
        $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image_name = basename($_FILES['image']['name']);
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_folder = 'item_images/' . $image_name;

            if (move_uploaded_file($image_tmp, $image_folder)) {
                $sql = "UPDATE items SET name = ?, description = ?, price = ?, stock_quantity = ?, image = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssdiss', $name, $description, $price, $quantity, $image_name, $id);
            } else {
                echo "<script>alert('Failed to upload new image.');</script>";
            }
        } else {
            $sql = "UPDATE items SET name = ?, description = ?, price = ?, stock_quantity = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssdii', $name, $description, $price, $quantity, $id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Item updated successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['delete'])) {
        // DELETE
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $sql = "DELETE FROM items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo "<script>alert('Item deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['edit'])) {
        // EDIT - Load item data into the form
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES);
        $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
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
    <title>Admin - Manage Catalog</title>
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
        input, textarea, button {
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
        <h2><?php echo $edit_mode ? "Edit Item" : "Add New Item"; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>
            <input type="text" name="name" placeholder="Item Name" value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" required>
            <textarea name="description" placeholder="Description" required><?php echo htmlspecialchars($description, ENT_QUOTES); ?></textarea>
            <input type="number" name="price" step="0.01" placeholder="Price" value="<?php echo $price; ?>" required>
            <input type="number" name="quantity" placeholder="Stock Quantity" value="<?php echo $quantity; ?>" required>
            <label for="image">Upload Image (Max 5 MB, optional):</label>
            <input type="file" name="image" id="image">
            <small style="color: gray;">Leave blank to use the default placeholder image.</small>
            <br>
            <?php if ($edit_mode): ?>
                <button type="submit" name="update">Update Item</button>
                <button type="submit" name="cancel">Cancel</button>
            <?php else: ?>
                <button type="submit" name="create">Add Item</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <h2>Catalog Items</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM items";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src="item_images/<?php echo htmlspecialchars($row['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>" style="width: 50px;"></td>
                    <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php
echo htmlspecialchars($row['id'], ENT_QUOTES); ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>">
                            <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($row['price'], ENT_QUOTES); ?>">
                            <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($row['stock_quantity'], ENT_QUOTES); ?>">
                            <button type="submit" name="edit">Edit</button>
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="7">No items found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        const imageInput = document.getElementById('image');
        imageInput.addEventListener('change', function () {
            const fileSize = this.files[0]?.size / 1024 / 1024;
            if (fileSize > 5) {
                alert('File size exceeds 5 MB. Please choose a smaller file.');
                this.value = '';
            }
        });
    </script>

    <?php $conn->close(); ?>
</body>
</html>
