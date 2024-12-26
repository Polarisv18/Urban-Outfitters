-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 08, 2024 at 06:53 AM
-- Server version: 8.0.35
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Final-Project`
--

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `stock_quantity` int UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Items`
--

INSERT INTO `Items` (`id`, `name`, `price`, `description`, `stock_quantity`, `image`) VALUES
(1, 'Silk Wrap Dress', 350.88, 'Wrap yourself in luxury with this sultry silk dress. Perfect for stealing the spotlight.', 6, 'silk_wrap_dress.jpg'),
(2, 'Tailored Wool Blazer', 499.99, 'Power dressing redefined. This tailored blazer says, \"I own the room.\"', 7, 'tailored_blazer.jpg'),
(3, 'Leather Stiletto Boots', 799.99, 'Step into these killer stilettos and slay every room you enter.', 6, 'black_boots.png'),
(4, 'Cashmere Turtleneck Sweater', 249.99, 'Cozy up like the diva you are. This cashmere sweater screams effortless chic.', 20, 'cashmere_sweater.jpg'),
(5, 'Quilted Leather Shoulder Bag', 999.99, 'A bag that’s as iconic as you. Chic quilting with unapologetic confidence.', 4, 'quilted_bag.jpg'),
(7, 'testing', 222.00, 'testing', 22, 'homepage.jpg'),
(9, 'Velvet Blazer', 129.99, 'Luxurious deep red velvet blazer, tailored to perfection.', 10, 'velvet_blazer.png'),
(10, 'Sequin Maxi Dress', 249.99, 'Dazzling midnight blue sequin gown with a dramatic slit.', 5, 'sequin_maxi_dress.png'),
(11, 'Leather Pants', 159.99, 'Edgy black leather pants, perfect for a bold look.', 8, 'leather_pants.png'),
(12, 'Oversized Sweater', 89.99, 'Cozy cream oversized sweater with cable-knit details.', 15, 'oversized_sweater.png'),
(13, 'Plaid Mini Skirt', 69.99, 'Chic tartan plaid mini skirt with a high-waisted fit.', 12, 'plaid_mini_skirt.png'),
(14, 'Denim Jacket', 99.99, 'Classic distressed light-wash denim jacket.', 10, 'denim_jacket.png'),
(15, 'Satin Midi Dress', 179.99, 'Elegant champagne satin dress with ruched sides.', 7, 'satin_midi_dress.png'),
(16, 'Tweed Blazer', 139.99, 'Sophisticated tweed blazer with metallic thread accents.', 9, 'tweed_blazer.png'),
(17, 'Silk Camisole', 59.99, 'Delicate silk camisole in blush pink with lace trim.', 20, 'silk_camisole.png'),
(18, 'Wide-Leg Trousers', 129.99, 'High-rise white wide-leg trousers with a tailored fit.', 8, 'wide_leg_trousers.png'),
(19, 'Corset Top', 89.99, 'Structured black corset top with boning details.', 15, 'corset_top.png'),
(20, 'Faux Fur Coat', 199.99, 'Lavish faux fur coat in ivory for glamorous evenings.', 6, 'faux_fur_coat.png'),
(21, 'Ruffled Blouse', 79.99, 'Romantic white blouse with layered ruffles and sheer sleeves.', 10, 'ruffled_blouse.png'),
(22, 'Graphic T-Shirt', 49.99, 'Trendy oversized tee with a bold graphic print.', 25, 'graphic_tshirt.png'),
(23, 'Ankle Boots', 129.99, 'Sleek black ankle boots with a block heel.', 12, 'ankle_boots.png');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `status`, `total_amount`, `shipping_address`) VALUES
(2, 2, '2024-12-08 05:08:41', 'Pending', 1673.97, ''),
(3, 2, '2024-12-08 05:10:45', 'Pending', 379.07, ''),
(4, 2, '2024-12-08 05:34:27', 'Pending', 350.99, '456 User Rd, User Town, User State, 67890'),
(5, 2, '2024-12-08 06:30:46', 'Cancelled', 1201.97, '456 User Rd, User Town, User State, 67890');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`) VALUES
(3, 2, 2, 1, 499.99),
(4, 2, 3, 1, 799.99),
(5, 2, 4, 1, 249.99),
(6, 3, 1, 1, 350.99),
(7, 4, 1, 1, 350.99),
(8, 5, 1, 2, 350.99),
(9, 5, 2, 1, 499.99);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') NOT NULL DEFAULT 'User',
  `phone_number` varchar(20) DEFAULT NULL,
  `shipping_address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `role`, `phone_number`, `shipping_address`) VALUES
(1, 'bsmith', 'Bob', 'Smith', 'bsmith@example.com', '$2y$10$nrS/CFPvZ/BGtOEly14b4ekFGMRdqzcJ7HiTglUbMDi187IooeATW', 'Admin', '1234567890', '123 Admin St, Admin City, Admin State, 12345'),
(2, 'pjones', 'Peter', 'Jones', 'pjones@example.com', '$2y$10$FANq4Ct9UErwtU4SO2HIuOkwh.K1R.EGT/yHqwk/0ksUnX02kiLcm', 'User', '9876543288', '456 User Rd, User Town, User State, 67890'),
(3, 'jdoe', 'John', 'Doe', 'johndoe@example.com', '$2y$10$ai9nDjJq.mDT1/WgLCCGSuXbIYTwZmlWgNvl2wGf8oOFaItT5qBYW', 'User', '5556667777', '789 Guest Ln, Guest Village, Guest State, 11223'),
(4, 'edavis', 'Emily', 'Davis', 'emilydavis@example.com', '$2y$10$vlaXlOW4yNNULz0EHvCBCOSOVSoBC6APZ3ghWi9TmeOETrCiFzzFW', 'User', '7776665555', '567 Fisher Rd, Fisher Town, Fisher State, 33445'),
(5, 'mrobinson', 'Matthew', 'Robinson', 'mrobinson@example.com', '$2y$10$tjaen9adyAc22g3rwVq/.ebN6T.NAMJEKxlniXiHvCuWl3C459/v6', 'User', '9998887777', '890 Spruce Blvd, Spruce Town, Spruce State, 44556'),
(6, 'jgarcia', 'James', 'Garcia', 'jamesgarcia@example.com', '$2y$10$YfaDj7.XDe6hjeVydLb6mu7TSv6ZxzzzhobsI6jCt2PBTpcn9./eW', 'User', '1112223333', '765 Maple Dr, Maple City, Maple State, 55667'),
(7, 'eclark', 'Emma', 'Clark', 'emmaclark@example.com', '$2y$10$ek/L.N//m2W9eulTZvuE5u.hsyHpM0gX/dVFOp5p0mUyOaFJp70Cm', 'User', '3332221111', '654 Cedar St, Cedar Town, Cedar State, 11223'),
(8, 'owalker', 'Olivia', 'Walker', 'oliviawalker@example.com', '$2y$10$wzhbx4fBPHmmB2N/1xOEfevErP13pENWeg3xk0U8oIa6W0K6XUqBS', 'User', '9994441111', '432 Elm Way, Elm City, Elm State, 33444'),
(9, 'rhall', 'Ryan', 'Hall', 'ryanhall@example.com', '$2y$10$7isLpG44QFG2ekBbitOzeeXGrnPiR1Xf2GQMDth2Ovd7Rf.XKi5My', 'User', '8883335555', '123 Oak Dr, Oak City, Oak State, 67890'),
(10, 'iyoung', 'Isabella', 'Young', 'isabellayoung@example.com', '$2y$10$0ABbjccHAgZk0gTNb/mLWuGonRk8Z6MMzlFLEOfdrPNthdWpE/ptq', 'User', '7776664444', '876 Birch St, Birch Town, Birch State, 55667'),
(11, 'aallen', 'Alexander', 'Allen', 'alexanderallen@example.com', '$2y$10$1oknOG9TRHzFqEhHlsOSGe83/j5H8AyF3RFHCTnzTva0xlYRBzJ5G', 'User', '5559991111', '890 Spruce Dr, Spruce Town, Spruce State, 44556'),
(12, 'aking', 'Ava', 'King', 'avaking@example.com', '$2y$10$BKqREosjxNB1Y2A9LLeKQ.oiB0fmxTB7YiAD7.eQ2ZkNr/GP.tukq', 'User', '2226661111', '765 Cypress Ln, Cypress City, Cypress State, 33445'),
(13, 'mwilson', 'Michael', 'Wilson', 'michaelwilson@example.com', '$2y$10$PuWqfoNvCOwfaywiX6kIoekvh3bPuzAIQnABM2xQ7YHVNNVM3bkFi', 'User', '1115553333', '567 Fisher Rd, Fisher City, Fisher State, 11223'),
(14, 'sjohnson', 'Sarah', 'Johnson', 'sarahjohnson@example.com', '$2y$10$dOA3JQe2xmfC7qdjafm7peCZnFT5I3L4tnCVH0JGCOmg4Y1nZyscO', 'User', '9992228888', '321 Pine Ln, Pine City, Pine State, 33445'),
(15, 'tmartinez', 'Laura', 'Martinez', 'lauramartinez@example.com', '$2y$10$McPxHgPsOCERHbAR7hTnC.7y6arzZ8vwezbnKdo0bm7N/VglYgiBu', 'User', '4443332222', '654 Maple Ln, Maple Village, Maple State, 55667'),
(16, 'smorales', 'Sophia', 'Morales', 'sophiamorales@example.com', '$2y$10$5epsUxlRj7wxZ3wrAlnxe.rautrAqfMWiozvkWGX1762NwqjtdBma', 'User', '1113335555', '123 Redwood Rd, Redwood City, Redwood State, 44556'),
(17, 'ajames', 'Andrew', 'James', 'andrewjames@example.com', '$2y$10$q5B.DpPdcNFSQ57jEIsyjeqOyYKGR55rqVEVy5jdX.I8zUHxDciTu', 'User', '8885554444', '890 Oak Dr, Oak City, Oak State, 67890'),
(18, 'klane', 'Kevin', 'Lane', 'kevinlane@example.com', '$2y$10$7WTyXFputrfk0T0lLoUYqenNA1jTSRCrJ0BWxie5CsDzIJqXzylhW', 'User', '5551114444', '765 Birch St, Birch City, Birch State, 55667'),
(19, 'mjackson', 'Maria', 'Jackson', 'mariajackson@example.com', '$2y$10$e32j2/DCdVGOxFB7ZPbkCuu/CUvx9IunrcIDTB4sJjKphdVQe4Rra', 'User', '7773339999', '321 Pine Ave, Pine Village, Pine State, 33445'),
(20, 'wroberts', 'William', 'Roberts', 'williamroberts@example.com', '$2y$10$t..K5/IHRa8DZP6Fm6ebC.BIMMyHi2FKkCYyP4egbEjmRQa8Btnea', 'User', '6667778888', '123 Cedar St, Cedar Village, Cedar State, 11223');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Items`
--
ALTER TABLE `Items`
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
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Items`
--
ALTER TABLE `Items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
