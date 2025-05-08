<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add to Cart (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo "Please log in first.";
        exit();
    }

    $product_id = intval($_POST['product_id']);
    $user_id = intval($_SESSION['user_id']);

    // Check if the user exists in the 'users' table
    $user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $user_check->bind_param("i", $user_id);
    $user_check->execute();
    $user_check->store_result();

    if ($user_check->num_rows === 0) {
        echo "Invalid user. Please log in.";
        $user_check->close();
        exit();
    }

    // Check if the product exists in the 'products' table
    $product_check = $conn->prepare("SELECT id, name, price, image, stock FROM products WHERE id = ?");
    $product_check->bind_param("i", $product_id);
    $product_check->execute();
    $product_check->store_result();

    if ($product_check->num_rows === 0) {
        echo "Product not found.";
        $product_check->close();
        exit();
    }
    // Fetch product details
    $product_check->bind_result($product_id, $product_name, $product_price, $product_image, $stock);
    $product_check->fetch();
    $product_check->close();
    if ($stock <= 0) {
      echo "Sorry, this product is out of stock.";
      exit();
    }

    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $stmt->bind_result($current_quantity);
      $stmt->fetch();
  
      if ($current_quantity >= $stock) {
          echo "Cannot add more. Stock limit reached.";
          $stmt->close();
          exit();
      }
  
      $stmt->close();
  
      // Update the quantity
      $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
      $update->bind_param("ii", $user_id, $product_id);
      $update->execute();
      $update->close();
    } else {
        // Product doesn't exist in the cart, so insert it
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, name, price, image, quantity) VALUES (?, ?, ?, ?, ?, 1)");
        $insert->bind_param("iisss", $user_id, $product_id, $product_name, $product_price, $product_image);
        $insert->execute();
        $insert->close();
    }
    echo "Product added to cart!";
    exit();
}

// AJAX Product Search (Starts With Search)
if (isset($_GET['ajax'])) {
    $search = $_GET['search'] ?? '';
    $search = "$search%";

    // SQL query with case-insensitive 'starts with' search
    $stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(name) LIKE LOWER(?)");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = "/comtech/assets/img/menu/" . htmlspecialchars($row['image']);
            echo '<div class="product-card">';
            echo '<div class="product-badge">' . (intval($row['stock']) > 0 ? '<span class="in-stock">In Stock</span>' : '<span class="out-of-stock">Out of Stock</span>') . '</div>';
            echo '<div class="product-image"><img src="' . $imagePath . '" alt="' . htmlspecialchars($row['name']) . '"></div>';
            echo '<div class="product-info">';
            echo '<h3 class="product-title">' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p class="product-description">' . htmlspecialchars($row['description']) . '</p>';
            echo '<div class="product-meta">';
            echo '<span class="product-price">NPR ' . number_format($row['price']) . '</span>';
            echo '<span class="product-stock">Stock: ' . intval($row['stock']) . '</span>';
            echo '</div>';
            echo '<div class="product-actions">';
            if (intval($row['stock']) > 0) {
                echo '<button class="add-to-cart-btn" onclick="addToCart(' . intval($row['id']) . ')"><i class="fas fa-shopping-cart"></i> Add to Cart</button>';
            } else {
                echo '<button class="out-of-stock-btn" disabled><i class="fas fa-times-circle"></i> Out of Stock</button>';
            }
            echo '<button class="quick-view-btn" onclick="quickView(' . intval($row['id']) . ')"><i class="fas fa-eye"></i></button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-results">';
        echo '<i class="fas fa-search"></i>';
        echo '<p>No products found matching "<span>' . htmlspecialchars($_GET['search']) . '</span>"</p>';
        echo '<p>Try a different search term or browse our categories</p>';
        echo '</div>';
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location:/comtech/home.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comtech - Shop Tech Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="productdisplay.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
        <a href="productdisplay.php" class="logo" style="display: flex; align-items: center; text-decoration: none; font-size: 24px; color: #333; font-weight: 600;">
    <img src="assets/img/logo.png" alt="Company Logo" style="width: 40px; height: 40px; margin-right: 10px;">
    Comtech
</a>

            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="search-input" class="search-input" placeholder="Search for products...">
            </div>
            
            <div class="nav-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user-circle"></i>
                        <span class="d-none d-md-inline">Profile</span>
                    </a>
                    
                    <button id="cart-button" class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="cart-count">0</span>
                    </button>
                    
                    <a href="?logout=true" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Shop Tech Products</h1>
            <p class="page-subtitle">Discover the latest technology products for your needs</p>
        </div>
        <div class="product-grid" id="product-container">
            <?php
            $query = "SELECT * FROM products";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = "/comtech/assets/img/menu/" . htmlspecialchars($row['image']);
                    echo '<div class="product-card">';
                    echo '<div class="product-badge">' . (intval($row['stock']) > 0 ? '<span class="in-stock">In Stock</span>' : '<span class="out-of-stock">Out of Stock</span>') . '</div>';
                    echo '<div class="product-image"><img src="' . $imagePath . '" alt="' . htmlspecialchars($row['name']) . '"></div>';
                    echo '<div class="product-info">';
                    echo '<h3 class="product-title">' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p class="product-description">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<div class="product-meta">';
                    echo '<span class="product-price">NPR ' . number_format($row['price']) . '</span>';
                    echo '<span class="product-stock">Stock: ' . intval($row['stock']) . '</span>';
                    echo '</div>';
                    echo '<div class="product-actions">';
                    if (intval($row['stock']) > 0) {
                        echo '<button class="add-to-cart-btn" onclick="addToCart(' . intval($row['id']) . ')"><i class="fas fa-shopping-cart"></i> Add to Cart</button>';
                    } else {
                        echo '<button class="out-of-stock-btn" disabled><i class="fas fa-times-circle"></i> Out of Stock</button>';
                    }
                    // echo '<button class="quick-view-btn" onclick="quickView(' . intval($row['id']) . ')"><i class="fas fa-eye"></i></button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-results">';
                echo '<i class="fas fa-box-open"></i>';
                echo '<p>No products available at the moment</p>';
                echo '<p>Please check back later for new arrivals</p>';
                echo '</div>';
            }
            $conn->close();
            ?>
        </div>
    </main>

    <!-- Notification -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span>Product added to cart successfully!</span>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            fetch(`productdisplay.php?ajax=1&search=${encodeURIComponent(query)}`)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('product-container').innerHTML = data;
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
        });

        // Add to cart functionality
        function addToCart(productId) {
            fetch("productdisplay.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `add_to_cart=1&product_id=${encodeURIComponent(productId)}`
            })
            .then(response => {
                if (response.status === 403) {
                    window.location.href = "login.php";
                    return Promise.reject("Login required");
                }
                return response.text();
            })
            .then(data => {
                if (data && data.includes("Product added to cart")) {
                    showNotification();
                    updateCartCount();
                } else {
                    alert(data);
                }
            })
            .catch(err => {
                if (err !== "Login required") {
                    console.error('Add to cart error:', err);
                    alert("Something went wrong. Please try again.");
                }
            });
        }

        // Quick view functionality
        function quickView(productId) {
            // This would typically open a modal with product details
            // For now, we'll just log to console
            console.log(`Quick view for product ID: ${productId}`);
            // You could implement a fetch request to get detailed product info
            // and display it in a modal
        }

        // Show notification
        function showNotification() {
            const notification = document.getElementById('notification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Update cart count
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count || 0;
                })
                .catch(err => {
                    console.error('Failed to update cart count:', err);
                });
        }

        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartCount();
            
            // Cart button click handler
            document.getElementById('cart-button').addEventListener('click', () => {
                window.location.href = "cart.php";
            });
            
            // Filter buttons functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            });
            
            const sortSelect = document.querySelector('.sort-select');
            sortSelect.addEventListener('change', () => {
                const sortValue = sortSelect.value;
            });
        });
    </script>
</body>
</html>
