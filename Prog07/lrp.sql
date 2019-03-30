-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2019 at 10:43 PM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.15-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lrp`
--

-- --------------------------------------------------------

--
-- Table structure for table `lrp_companies`
--

CREATE TABLE `lrp_companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lrp_companies`
--

INSERT INTO `lrp_companies` (`company_id`, `company_name`) VALUES
(1, 'Aldi'),
(2, 'Meijer'),
(3, 'Wal-Mart'),
(4, 'Beyond Measure'),
(5, 'Amazon'),
(6, 'Kroger');

-- --------------------------------------------------------

--
-- Table structure for table `lrp_items`
--

CREATE TABLE `lrp_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_price` decimal(5,2) NOT NULL,
  `store_id` int(11) NOT NULL,
  `item_quantity` double NOT NULL,
  `item_quantity_unit` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_ppq` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lrp_items`
--

INSERT INTO `lrp_items` (`item_id`, `item_name`, `item_price`, `store_id`, `item_quantity`, `item_quantity_unit`, `item_ppq`) VALUES
(2, 'Vegtable Oil', '1.85', 1, 48, 'oz', 0.03854166666666667),
(3, 'Vegtable Oil', '2.69', 2, 48, 'oz', 0.05604166666666666),
(4, 'Butter', '2.65', 1, 1, 'lb', 2.65),
(5, '1 to 1 Flour', '3.20', 3, 1, 'lb', 3.2),
(6, 'aa', '12.00', 1, 2, 'oz', 6),
(7, 'qwe', '2.99', 3, 12, 'oz', 0.24916666666666668);

-- --------------------------------------------------------

--
-- Table structure for table `lrp_stores`
--

CREATE TABLE `lrp_stores` (
  `store_id` int(11) NOT NULL,
  `store_city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lrp_stores`
--

INSERT INTO `lrp_stores` (`store_id`, `store_city`, `company_id`) VALUES
(1, 'Midland', 1),
(2, 'Midland', 2),
(3, 'Midland', 4);

-- --------------------------------------------------------

--
-- Table structure for table `lrp_users`
--

CREATE TABLE `lrp_users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lrp_users`
--

INSERT INTO `lrp_users` (`user_id`, `user_name`, `user_email`, `user_password_hash`) VALUES
(1, NULL, 'jj@jj.jj', 'bf2bc2545a4a5f5683d9ef3ed0d977e0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lrp_companies`
--
ALTER TABLE `lrp_companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `lrp_items`
--
ALTER TABLE `lrp_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `lrp_stores`
--
ALTER TABLE `lrp_stores`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `lrp_users`
--
ALTER TABLE `lrp_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lrp_companies`
--
ALTER TABLE `lrp_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `lrp_items`
--
ALTER TABLE `lrp_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `lrp_stores`
--
ALTER TABLE `lrp_stores`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `lrp_users`
--
ALTER TABLE `lrp_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
