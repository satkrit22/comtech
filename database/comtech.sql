-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 01:53 PM
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
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `activity_type`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'profile_view', 'Viewed profile page', '::1', '2025-05-10 10:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `name`, `email`, `phone`, `address`, `bio`, `profile_image`, `last_login`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrator', 'admin@comtech.com', '+977 1234567890', '', '', NULL, '2025-05-10 16:06:19', '$2y$10$KbZ4U19kbb5BL6m1V1kkzuP91ZdskKDcaKLf0uT0xn4Ja4ueyK.9m', '2025-05-02 06:46:27', '2025-05-10 10:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity`
--

CREATE TABLE `admin_activity` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity`
--

INSERT INTO `admin_activity` (`id`, `admin_id`, `activity_type`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:28:54'),
(2, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:30:01'),
(3, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:30:13'),
(4, 1, 'profile_update', 'Updated profile information', '::1', '2025-05-10 10:31:45'),
(5, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:31:45'),
(6, 1, 'profile_update', 'Updated profile information', '::1', '2025-05-10 10:33:04'),
(7, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:33:04'),
(8, 1, 'profile_update', 'Updated profile information', '::1', '2025-05-10 10:33:58'),
(9, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:33:58'),
(10, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:34:10'),
(11, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:34:16'),
(12, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:36:50'),
(13, 1, 'profile_view', 'Viewed admin profile page', '::1', '2025-05-10 10:38:06');

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
(1, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address. jorpati, kathmandu, Province No. 1, Nepal', 'Dell Inspirion 3430 (90000 x 1) - ', 90000, 'pending', '2025-05-03 15:27:52'),
(2, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address. jorpati, kathmandu, Province No. 2, Nepal', 'Link PC+ (13500 x 3) - ', 40500, 'pending', '2025-05-04 15:39:38'),
(3, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address. jorpati, kathmandu, Province No. 2, Nepal', 'Laptop Cooler (1500 x 1) - Mouse (1500 x 1) - ', 3000, 'pending', '2025-05-04 15:45:56'),
(4, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address. jorpati, kathmandu, Karnali Province, Nepal', 'Laptop Cooler (1500 x 8) - ', 12150, 'pending', '2025-05-04 15:50:07'),
(5, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'Cash on Delivery', 'Address: jorpati, kathmandu, Bagmati Province, Nepal', 'Link PC+ (13500 x 2) - ', 27000, 'pending', '2025-05-05 14:56:02'),
(6, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'Cash on Delivery', 'Address: jorpati, kathmandu, Other, Nepal', 'Link PC (10500 x 2) - Link PC+ (13500 x 2) - ', 48200, 'processing', '2025-05-05 15:01:16'),
(7, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'cash on delivery', 'Address: jorpati, kathmandu, Bagmati Province, Nepal', 'Link PC+ (13500 x 4) - Mouse (1500 x 4) - ', 60000, 'completed', '2025-05-09 15:00:21'),
(8, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'Esewa', 'Address: jorpati, kathmandu, Bagmati Province, Nepal', 'Mouse (1500 x 1) - Link PC (10500 x 1) - ', 12000, 'pending', '2025-05-10 11:22:49'),
(9, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'Esewa', 'Address: jorpati, kathmandu, Other, Nepal', 'Mouse (1500 x 1) - ', 1700, 'pending', '2025-05-10 11:29:04'),
(10, 1, 'Satkrit Bhandari', '9818400974', 'satkritbhandari11@gmail.com', 'Cash on Delivery', 'Address: jorpati, kathmandu, Bagmati Province, Nepal', 'Link PC+ (13500 x 1) - CPU Fan (500 x 1) - ', 14000, 'pending', '2025-05-10 11:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 5, 2, 2, 13500.00),
(2, 6, 1, 2, 10500.00),
(3, 6, 2, 2, 13500.00),
(4, 7, 2, 4, 13500.00),
(5, 7, 4, 4, 1500.00),
(6, 8, 4, 1, 1500.00),
(7, 8, 1, 1, 10500.00),
(8, 9, 4, 1, 1500.00),
(9, 10, 2, 1, 13500.00),
(10, 10, 20, 1, 500.00);

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
(1, 'Link PC', 'Basic Thin client device for everyday tasks', 10500, 198, 'menu-item-1.jpg', 2, '2025-05-03 11:23:59'),
(2, 'Link PC+', 'Thin client device for everyday tasks with better performance', 13500, 138, 'menu-item-1.jpg', 2, '2025-05-03 11:23:59'),
(3, 'Laptop Cooler', 'Cooling pad for laptops', 1500, 21, 'menu-item-6.jpg', 4, '2025-05-03 11:23:59'),
(4, 'Mouse', 'Standard optical USB mouse', 1500, 20, 'menu-item-4.png', 3, '2025-05-03 11:23:59'),
(5, 'Clamper', 'Cable organizer clamp', 1300, 50, 'menu-item-5.jpg', 3, '2025-05-03 11:23:59'),
(6, 'Keyboard', 'Wired USB keyboard', 3200, 10, 'menu-item-3.jpg', 3, '2025-05-03 11:23:59'),
(7, 'Normal Server', 'Entry-level server system', 90500, 18, 'prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png', 2, '2025-05-03 11:23:59'),
(8, 'Network Cable', 'High-quality Ethernet cable', 19500, 12, 'DHU7060_DH-PFM920I-5EUN_product-image_1.png', 2, '2025-05-03 11:23:59'),
(9, 'Solid State Drive 128GB', '128GB SATA SSD storage', 2200, 25, 'DAHUA-SATA-256GB-3 (1).png', 3, '2025-05-03 11:23:59'),
(10, 'Dell Monitor', 'Dell 18.5-inch HD monitor', 15500, 22, 'Dell-D1918H.jpg', 3, '2025-05-03 11:23:59'),
(11, 'Dell Keyboard & Mouse', 'Dell wired keyboard & mouse combo', 3200, 16, 'kb216-ms116-kbm-01-bk-1.png', 3, '2025-05-03 11:23:59'),
(12, 'Power Supply', 'Standard 350W power supply unit', 1500, 8, 'FSP350-60EPN80-lg__34378.jpg', 3, '2025-05-03 11:23:59'),
(13, 'External Harddisk 1TB', '1TB external storage device', 5700, 14, '5e47a7e207605426186502e15be08e22.jpg', 3, '2025-05-03 11:23:59'),
(14, 'Dell Inspirion 3430', 'Dell Inspirion laptop model 3430', 90000, 2, '3430_.jpg', 4, '2025-05-03 11:23:59'),
(15, 'NVME SSD 128GB', '128GB high-speed NVME SSD', 3000, 7, 'HP_1TB_SSD.jpg', 3, '2025-05-03 11:23:59'),
(16, 'Headphone', 'Wired over-ear headphones', 1500, 9, '84cf6d5739a034f0b28023fb91453a2e.jpg', 4, '2025-05-03 11:23:59'),
(17, 'Caddy', 'Laptop HDD/SSD mounting caddy', 500, 20, 'hdd_caddy_1.jpg', 3, '2025-05-03 11:23:59'),
(18, 'Wifi Dongle', 'USB wireless network adapter', 500, 11, '4050158915.jpg', 3, '2025-05-03 11:23:59'),
(19, 'Ethernet Adapter', 'USB to Ethernet network adapter', 800, 13, '71-E1Mu48WL._AC_SL1500_.jpg', 3, '2025-05-03 11:23:59'),
(20, 'CPU Fan', 'Cooling fan for processors', 500, 5, 'main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg', 3, '2025-05-03 11:23:59'),
(21, 'HDMI to VGA Converter', 'HDMI to VGA video converter', 500, 27, 'hdmi.jpg', 3, '2025-05-03 11:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT 1,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(200) NOT NULL,
  `address` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `Name`, `Email`, `Phone`, `address`, `bio`, `profile_image`, `last_login`, `password`, `created_at`, `updated_at`) VALUES
(1, 1, 'Satkrit Bhandari', 'satkritbhandari11@gmail.com', '9818400974', NULL, NULL, NULL, NULL, '$2y$10$l.OmEkVFbd.f/z0/CY5SM.kGEpaI0qogYZmfNRXdELnOEFoE4e3km', '2025-05-02 04:38:39', '2025-05-10 10:17:29'),
(3, 1, 'Nabin BK', 'nabinbk@gmail.com', '9767934698', NULL, NULL, NULL, NULL, '$2y$10$6.OqYT5pzLJDJ0yn7ir7DuhWye29p.4k9ftTtL2BnLl3HFXz7tBhm', '2025-05-10 08:53:42', '2025-05-10 10:17:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `activity_type` (`activity_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_activity`
--
ALTER TABLE `admin_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `activity_type` (`activity_type`),
  ADD KEY `created_at` (`created_at`);

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
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_activity`
--
ALTER TABLE `admin_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
