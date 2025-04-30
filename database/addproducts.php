<?php
require 'connectdatabase.php'; // database connection

$products = [
    ['Link PC', 'Description for product 1', 10500, '/assets/img/menu/menu-item-1.jpg', 200],
    ['Link PC+', 'Description for product 2',13500, '/assets/img/menu/menu-item-1.jpg', 150],
    ['Laptop Cooler', 'Description for product 3', 1500,  '/assets/img/menu/menu-item-6.jpg', 30],
    ['Mouse', 'Description for product 4', 1500, '/assets/img/menu/menu-item-4.jpg', 25],
    ['Clamper', 'Description for product 5', 1300,  '/assets/img/menu/menu-item-5.jpg', 50],
    ['Keyboard', 'Description for product 6', 3200, '/assets/img/menu/menu-item-3.jpg', 10],
    ['Normal Server', 'Description for product 7',90500, '/assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png', 18],
    ['Network Cable', 'Description for product 8', 19500,  '/assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png', 12],
    ['Solid State Drive 128GB', 'Description for product 9', 2200, '/assets/img/menu/DAHUA-SATA-256GB-3 (1).png', 25],
    ['Dell Monitor','Description for product 10',15500,  '/assets/img/menu/Dell-D1918H.jpg',22],
    ['Dell Keyboard & Mouse','Description for product 11',3200, '/assets/img/menu/kb216-ms116-kbm-01-bk-1.png',16],
    ['Power Supply','Description for product 12',1500, '/assets/img/menu/FSP350-60EPN80-lg__34378.jpg',8],
    ['External Harddisk 1TB','Description for product 13',5700, '/assets/img/menu/5e47a7e207605426186502e15be08e22.jpg',14],
    ['Dell Inspirion 3430','Description for product 14',90000,  '/assets/img/menu/3430_.jpg',1],
    ['NVME SSD 128GB','Description for product 15',3000, '/assets/img/menu/HP_1TB_SSD.jpg',7],
    ['Headphone','Description for product 16',1500, '/assets/img/menu/84cf6d5739a034f0b28023fb91453a2e.jpg',9],
    ['Caddy','Description for product 17',500,  '/assets/img/menu/hdd_caddy_1.jpg',20],
    ['Wifi Dongle','Description for product 18',500, '/assets/img/menu/4050158915.jpg',11],
    ['Ethernet Adapter','Description for product 19',800, '/assets/img/menu/71-E1Mu48WL._AC_SL1500_.jpg',13],
    ['CPU Fan','Description for product 20',500, '/assets/img/menu/main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg',6],
    ['HDMI to VGA Converter','Description for product 21',500,  '/assets/img/menu/hdmi.jpg',27]
];

$stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");

foreach ($products as $product) {
    $stmt->execute($product);
}

echo "21 products inserted successfully!";
?>
