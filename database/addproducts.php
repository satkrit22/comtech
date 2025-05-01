<?php
require 'connectdatabase.php'; // make sure $conn is defined here

$products = [
    ['Link PC', 10500, '/assets/img/menu/menu-item-1.jpg', 200, 'linkpc'],
    ['Link PC+', 13500, '/assets/img/menu/menu-item-1.jpg', 150, 'linkpc'],
    ['Laptop Cooler', 1500,  '/assets/img/menu/menu-item-6.jpg', 30, 'laptop accessories'],
    ['Mouse', 1500, '/assets/img/menu/menu-item-4.jpg', 25, 'computer&accessories'],
    ['Clamper', 1300,  '/assets/img/menu/menu-item-5.jpg', 50, 'computer&accessories'],
    ['Keyboard', 3200, '/assets/img/menu/menu-item-3.jpg', 10, 'computer&accessories'],
    ['Normal Server', 90500, '/assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png', 18, 'linkpc'],
    ['Network Cable', 19500,  '/assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png', 12, 'linkpc'],
    ['Solid State Drive 128GB', 2200, '/assets/img/menu/DAHUA-SATA-256GB-3 (1).png', 25, 'computer&accessories'],
    ['Dell Monitor', 15500,  '/assets/img/menu/Dell-D1918H.jpg', 22, 'computer&accessories'],
    ['Dell Keyboard & Mouse', 3200, '/assets/img/menu/kb216-ms116-kbm-01-bk-1.png', 16, 'computer&accessories'],
    ['Power Supply', 1500, '/assets/img/menu/FSP350-60EPN80-lg__34378.jpg', 8, 'computer&accessories'],
    ['External Harddisk 1TB', 5700, '/assets/img/menu/5e47a7e207605426186502e15be08e22.jpg', 14, 'computer&accessories'],
    ['Dell Inspirion 3430', 90000,  '/assets/img/menu/3430_.jpg', 1, 'laptop accessories'],
    ['NVME SSD 128GB', 3000, '/assets/img/menu/HP_1TB_SSD.jpg', 7, 'computer&accessories'],
    ['Headphone', 1500, '/assets/img/menu/84cf6d5739a034f0b28023fb91453a2e.jpg', 9, 'laptop accessories'],
    ['Caddy', 500,  '/assets/img/menu/hdd_caddy_1.jpg', 20, 'computer&accessories'],
    ['Wifi Dongle', 500, '/assets/img/menu/4050158915.jpg', 11, 'computer&accessories'],
    ['Ethernet Adapter', 800, '/assets/img/menu/71-E1Mu48WL._AC_SL1500_.jpg', 13, 'computer&accessories'],
    ['CPU Fan', 500, '/assets/img/menu/main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg', 6, 'computer&accessories'],
    ['HDMI to VGA Converter', 500,  '/assets/img/menu/hdmi.jpg', 27, 'computer&accessories']
];

// Prepare statement with the category field
$stmt = $conn->prepare("INSERT INTO products (name, price, image, stock, category) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisis", $name, $price, $image, $stock, $category);

// Insert each product
foreach ($products as $product) {
    $name = $product[0];
    $price = $product[1];
    $image = $product[2];
    $stock = $product[3];
    $category = $product[4]; // Add the category
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "21 products inserted successfully!";
?>
