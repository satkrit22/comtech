-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 05:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comtech`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$KbZ4U19kbb5BL6m1V1kkzuP91ZdskKDcaKLf0uT0xn4Ja4ueyK.9m', '2025-05-02 06:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'ALL', '2025-05-02 07:51:10'),
(2, 'Link PC', '2025-05-02 07:51:10'),
(3, 'Computer Accessories', '2025-05-02 07:51:10'),
(4, 'Laptop & Accessories', '2025-05-02 07:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `total_products` text NOT NULL,
  `total_price` int(11) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address. jorpati, kathmandu, Province No. 1, Nepal', 'Dell Inspirion 3430 (90000 x 1) - ', 90000, 'pending', '2025-05-03 15:27:52');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `category_id`, `created_at`) VALUES
(1, 'Link PC', 'Basic Thin client device for everyday tasks', 10500, 200, 'menu-item-1.jpg', 2, '2025-05-03 11:23:59'),
(2, 'Link PC+', 'Thin client device for everyday tasks with better performance', 13500, 150, 'menu-item-1.jpg', 2, '2025-05-03 11:23:59'),
(3, 'Laptop Cooler', 'Cooling pad for laptops', 1500, 30, 'menu-item-6.jpg', 4, '2025-05-03 11:23:59'),
(4, 'Mouse', 'Standard optical USB mouse', 1500, 25, 'menu-item-4.png', 3, '2025-05-03 11:23:59'),
(5, 'Clamper', 'Cable organizer clamp', 1300, 50, 'menu-item-5.jpg', 3, '2025-05-03 11:23:59'),
(6, 'Keyboard', 'Wired USB keyboard', 3200, 10, 'menu-item-3.jpg', 3, '2025-05-03 11:23:59'),
(7, 'Normal Server', 'Entry-level server system', 90500, 18, 'prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png', 2, '2025-05-03 11:23:59'),
(8, 'Network Cable', 'High-quality Ethernet cable', 19500, 12, 'DHU7060_DH-PFM920I-5EUN_product-image_1.png', 2, '2025-05-03 11:23:59'),
(9, 'Solid State Drive 128GB', '128GB SATA SSD storage', 2200, 25, 'DAHUA-SATA-256GB-3 (1).png', 3, '2025-05-03 11:23:59'),
(10, 'Dell Monitor', 'Dell 18.5-inch HD monitor', 15500, 22, 'Dell-D1918H.jpg', 3, '2025-05-03 11:23:59'),
(11, 'Dell Keyboard & Mouse', 'Dell wired keyboard & mouse combo', 3200, 16, 'kb216-ms116-kbm-01-bk-1.png', 3, '2025-05-03 11:23:59'),
(12, 'Power Supply', 'Standard 350W power supply unit', 1500, 8, 'FSP350-60EPN80-lg__34378.jpg', 3, '2025-05-03 11:23:59'),
(13, 'External Harddisk 1TB', '1TB external storage device', 5700, 14, '5e47a7e207605426186502e15be08e22.jpg', 3, '2025-05-03 11:23:59'),
(14, 'Dell Inspirion 3430', 'Dell Inspirion laptop model 3430', 90000, 0, '3430_.jpg', 4, '2025-05-03 11:23:59'),
(15, 'NVME SSD 128GB', '128GB high-speed NVME SSD', 3000, 7, 'HP_1TB_SSD.jpg', 3, '2025-05-03 11:23:59'),
(16, 'Headphone', 'Wired over-ear headphones', 1500, 9, '84cf6d5739a034f0b28023fb91453a2e.jpg', 4, '2025-05-03 11:23:59'),
(17, 'Caddy', 'Laptop HDD/SSD mounting caddy', 500, 20, 'hdd_caddy_1.jpg', 3, '2025-05-03 11:23:59'),
(18, 'Wifi Dongle', 'USB wireless network adapter', 500, 11, '4050158915.jpg', 3, '2025-05-03 11:23:59'),
(19, 'Ethernet Adapter', 'USB to Ethernet network adapter', 800, 13, '71-E1Mu48WL._AC_SL1500_.jpg', 3, '2025-05-03 11:23:59'),
(20, 'CPU Fan', 'Cooling fan for processors', 500, 6, 'main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg', 3, '2025-05-03 11:23:59'),
(21, 'HDMI to VGA Converter', 'HDMI to VGA video converter', 500, 27, 'hdmi.jpg', 3, '2025-05-03 11:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Name`, `Email`, `Phone`, `password`, `created_at`) VALUES
(1, 'Satkrit Bhandari', 'satkritbhandari11@gmail.com', '9818400974', '$2y$10$l.OmEkVFbd.f/z0/CY5SM.kGEpaI0qogYZmfNRXdELnOEFoE4e3km', '2025-05-02 04:38:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Phone` (`Phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
