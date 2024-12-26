<?php
session_start();

// Check if sessions exist for mannequin and cart
if (!isset($_SESSION['mannequin_items'])) {
    $_SESSION['mannequin_items'] = [];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$mannequin_items = $_SESSION['mannequin_items'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Mannequin - Suburban Outfitters</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Archivo', sans-serif;
            margin: 0;
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
        .mannequin-container {
            margin-top: 20px;
            position: relative;
            width: 300px;
            height: 600px;
            background-image: url('images/mannequin_base.png');
            background-size: cover;
            border: 2px solid #000;
            border-radius: 10px;
            overflow: hidden;
        }
        .styled-item {
            position: absolute;
            cursor: grab;
            user-select: none;
            overflow: visible;
            width: 100px;
        }
        .styled-item img {
            width: 100%;
            height: auto;
        }
        .styled-item:active {
            cursor: grabbing;
        }
        .resize-handle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 15px;
            height: 15px;
            background-color: #000;
            cursor: nwse-resize;
            z-index: 1001;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .actions button {
            padding: 10px 20px;
            border: 2px solid #000;
            background-color: transparent;
            color: #000;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .actions button:hover {
            background-color: #000;
            color: #fff;
        }
    </style>
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
    <h1>Virtual Mannequin</h1>
    <div class="mannequin-container" id="mannequin">
        <?php foreach ($mannequin_items as $index => $item): ?>
            <div 
                class="styled-item" 
                id="item-<?php echo $index; ?>" 
                style="top: <?php echo 100 + ($index * 50); ?>px; left: 50px;"
            >
                <img 
                    src="item_images/<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>" 
                    alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>"
                >
                <div class="resize-handle"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <form method="POST" action="add_to_cart.php">
            <?php foreach ($mannequin_items as $item): ?>
                <input type="hidden" name="mannequin_items[]" value="<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES); ?>">
            <?php endforeach; ?>
            <button type="submit" name="add_all_to_cart">Add All to Cart</button>
        </form>
        <form method="POST" action="clear_mannequin.php">
            <button type="submit">Clear Mannequin</button>
        </form>
        <a href="catalog.php">
            <button type="button">Add More Items</button>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mannequin = document.getElementById('mannequin');
            const styledItems = document.querySelectorAll('.styled-item');

            styledItems.forEach(item => {
                let isDragging = false;
                let isResizing = false;
                let startX, startY, initialX, initialY, initialWidth;

                // Dragging functionality
                item.addEventListener('mousedown', (e) => {
                    if (e.target.classList.contains('resize-handle')) return; // Skip if resizing
                    e.preventDefault();
                    isDragging = true;

                    startX = e.clientX;
                    startY = e.clientY;
                    const rect = item.getBoundingClientRect();
                    initialX = rect.left - mannequin.getBoundingClientRect().left;
                    initialY = rect.top - mannequin.getBoundingClientRect().top;

                    item.style.zIndex = "1000";
                });

                document.addEventListener('mousemove', (e) => {
                    if (isDragging) {
                        const deltaX = e.clientX - startX;
                        const deltaY = e.clientY - startY;

                        const newX = Math.max(0, Math.min(initialX + deltaX, mannequin.offsetWidth - item.offsetWidth));
                        const newY = Math.max(0, Math.min(initialY + deltaY, mannequin.offsetHeight - item.offsetHeight));

                        item.style.left = `${newX}px`;
                        item.style.top = `${newY}px`;
                    }

                    if (isResizing) {
                        const deltaWidth = e.clientX - startX;
                        const newWidth = Math.max(50, initialWidth + deltaWidth);

                        item.style.width = `${newWidth}px`;
                    }
                });

                document.addEventListener('mouseup', () => {
                    isDragging = false;
                    isResizing = false;
                    item.style.zIndex = "1";
                });

                const resizeHandle = item.querySelector('.resize-handle');
                resizeHandle.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    isResizing = true;

                    startX = e.clientX;
                    initialWidth = item.offsetWidth;
                });
            });
        });
    </script>
</body>
</html>
