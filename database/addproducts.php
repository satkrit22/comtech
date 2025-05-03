<?php
require 'connectdatabase.php'; // make sure $conn is defined here

$products = [
    ['Link PC', 'Basic Thin client device for everyday tasks', 10500, 'menu-item-1.jpg', 200, 'Link PC'],
    ['Link PC+', 'Thin client device for everyday tasks with better performance', 13500, 'menu-item-1.jpg', 150, 'Link PC'],
    ['Laptop Cooler', 'Cooling pad for laptops', 1500, 'menu-item-6.jpg', 30, 'Laptop & Accessories'],
    ['Mouse', 'Standard optical USB mouse', 1500, 'menu-item-4.png', 25, 'Computer Accessories'],
    ['Clamper', 'Cable organizer clamp', 1300, 'menu-item-5.jpg', 50, 'Computer Accessories'],
    ['Keyboard', 'Wired USB keyboard', 3200, 'menu-item-3.jpg', 10, 'Computer Accessories'],
    ['Normal Server', 'Entry-level server system', 90500, 'prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png', 18, 'Link PC'],
    ['Network Cable', 'High-quality Ethernet cable', 19500, 'DHU7060_DH-PFM920I-5EUN_product-image_1.png', 12, 'Link PC'],
    ['Solid State Drive 128GB', '128GB SATA SSD storage', 2200, 'DAHUA-SATA-256GB-3 (1).png', 25, 'Computer Accessories'],
    ['Dell Monitor', 'Dell 18.5-inch HD monitor', 15500, 'Dell-D1918H.jpg', 22, 'Computer Accessories'],
    ['Dell Keyboard & Mouse', 'Dell wired keyboard & mouse combo', 3200, 'kb216-ms116-kbm-01-bk-1.png', 16, 'Computer Accessories'],
    ['Power Supply', 'Standard 350W power supply unit', 1500, 'FSP350-60EPN80-lg__34378.jpg', 8, 'Computer Accessories'],
    ['External Harddisk 1TB', '1TB external storage device', 5700, '5e47a7e207605426186502e15be08e22.jpg', 14, 'Computer Accessories'],
    ['Dell Inspirion 3430', 'Dell Inspirion laptop model 3430', 90000, '3430_.jpg', 1, 'Laptop & Accessories'],
    ['NVME SSD 128GB', '128GB high-speed NVME SSD', 3000, 'HP_1TB_SSD.jpg', 7, 'Computer Accessories'],
    ['Headphone', 'Wired over-ear headphones', 1500, '84cf6d5739a034f0b28023fb91453a2e.jpg', 9, 'Laptop & Accessories'],
    ['Caddy', 'Laptop HDD/SSD mounting caddy', 500, 'hdd_caddy_1.jpg', 20, 'Computer Accessories'],
    ['Wifi Dongle', 'USB wireless network adapter', 500, '4050158915.jpg', 11, 'Computer Accessories'],
    ['Ethernet Adapter', 'USB to Ethernet network adapter', 800, '71-E1Mu48WL._AC_SL1500_.jpg', 13, 'Computer Accessories'],
    ['CPU Fan', 'Cooling fan for processors', 500, 'main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg', 6, 'Computer Accessories'],
    ['HDMI to VGA Converter', 'HDMI to VGA video converter', 500, 'hdmi.jpg', 27, 'Computer Accessories']
];

// First, get category IDs from the categories table
$category_ids = [];
$result = $conn->query("SELECT id, name FROM categories");
while ($row = $result->fetch_assoc()) {
    $category_ids[$row['name']] = $row['id'];
}

// Update your statement to include the description (second item)
$stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock, category_id) VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssisis", $name, $description, $price, $image, $stock, $category_id);

// Insert each product
foreach ($products as $product) {
    $name = $product[0];
    $description = $product[1];
    $price = $product[2];
    $image = $product[3];
    $stock = $product[4];
    $category_name = $product[5];
    
    // Get the category_id based on the category name
    $category_id = isset($category_ids[$category_name]) ? $category_ids[$category_name] : null;
    
    // Ensure category_id exists before inserting the product
    if ($category_id !== null) {
        $stmt->execute();
    } else {
        echo "Category '{$category_name}' not found.\n";
    }
}

$stmt->close();
$conn->close();

echo "Products inserted successfully!";
?>
