-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 02:20 AM
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
-- Database: `es_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 3, 'sheesh', '2025-05-05 17:23:14'),
(2, 1, 5, 'I want to feel happy', '2025-05-09 11:08:03');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'uploads/default_menu.jpg',
  `available` tinyint(1) DEFAULT 1,
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image`, `available`, `stock_quantity`) VALUES
(1, 'Beef Burger', 'Juicy grilled beef patty with lettuce, tomato, and cheese.', 129.00, 'uploads/burger.jpg', 1, 18),
(2, 'Chicken Alfredo Pasta', 'Creamy Alfredo pasta with grilled chicken breast.', 189.00, 'uploads/pasta.jpg', 1, 19),
(3, 'Iced Coffee', 'Cold brewed coffee with milk and sugar.', 89.00, 'uploads/iced_coffee.jpg', 1, 20),
(4, 'Classic Margherita Pizza', 'Tomato, mozzarella, and basil on a crispy crust.', 249.00, 'uploads/pizza.jpg', 1, 20),
(5, 'Mango Smoothie', 'Fresh mango blended with ice and milk.', 99.00, 'uploads/mango_smoothie.jpg', 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(10,2) NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'pending',
  `receipt` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `menu_item_id`, `quantity`, `price`, `total`, `ordered_at`, `total_price`, `order_status`, `receipt`) VALUES
(1, 3, 1, 1, NULL, NULL, '2025-05-01 12:07:40', 129.00, 'done', NULL),
(2, 3, 1, 1, NULL, NULL, '2025-05-01 12:22:23', 129.00, 'done', NULL),
(3, 3, 1, 1, NULL, NULL, '2025-05-01 12:26:54', 129.00, 'done', NULL),
(4, 3, 1, 1, NULL, NULL, '2025-05-06 15:09:20', 129.00, 'Pending', NULL),
(5, 3, 1, 1, NULL, NULL, '2025-05-06 15:09:20', 129.00, 'Pending', NULL),
(6, 3, 1, 2, NULL, NULL, '2025-05-06 15:10:00', 258.00, 'Pending', NULL),
(7, 4, 1, 2, NULL, NULL, '2025-05-07 02:41:22', 258.00, 'Pending', NULL),
(8, 3, 1, 1, NULL, NULL, '2025-05-09 01:58:17', 129.00, 'Pending', NULL),
(9, 3, 1, 1, NULL, NULL, '2025-05-08 20:14:37', 0.00, 'pending', NULL),
(10, 3, 2, 1, NULL, NULL, '2025-05-08 20:14:37', 0.00, 'pending', NULL),
(11, 3, 1, 2, NULL, NULL, '2025-05-11 17:11:00', 0.00, 'done', 'RCPT-68212eaa6fd35'),
(12, 3, 2, 1, NULL, NULL, '2025-05-11 17:11:00', 0.00, 'done', 'RCPT-68213147edaea');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `fullname`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'Niwresh_', 'Sherwin', 'Lumakang', 'sherwinlumakang827@gmail.com', '$2y$10$jA8XVKShBVXWC/QzFqp2heaFtRRweHYok4b9s4cDG4bNzlW.CrlGm', '2025-05-01 02:19:35', 'user'),
(2, 'Mr. Wew', 'Sherwin', 'Lumakang', 'sherwinlumakang1@gmail.com', '$2y$10$zVNZh0OzSbrdhnl9E40Sf.S1ttkh6Dw5bjlVzUGSliuL5HDGNV3me', '2025-05-01 02:22:01', 'user'),
(3, 'sherwin', 'Sherwin', 'Lumakang', 'Niwresh_@gmail.com', '$2y$10$ZzONR/VYDCKrSQRgSTG0sevvCdTm/30G2Oe3uUIKD063LjM3Dpn9G', '2025-05-01 02:23:55', 'user'),
(4, 'admin', 'admin', 'admin', 'Admin@gmail.com', '$2y$10$70Q.Qf/.EHRdflADF5m7mejLJiRLVkud//l4P9WD/vjwIoHLS0lQ.', '2025-05-06 13:44:16', 'admin'),
(6, 'Staff 1', 'Staff', '1', 'Staff1@gmail.com', '$2y$10$BryBxg5uftG3xCIknQe55eT58R2BZ0dJMBmDLz.yviC0pfcI2GVw6', '2025-05-09 02:40:33', 'staff'),
(7, 'Staff 2', 'Sherwin', 'Lumakang', 'Staff2@gmail.com', '$2y$10$shGoc6ELziV8XmmGhTfePenXeZLtl8vJVovCUXW2bQqiqkSJEXFrq', '2025-05-11 21:46:20', 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
