-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-03-24 06:58:21
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `d1280763`
--

-- --------------------------------------------------------

--
-- 資料表結構 `ad`
--

CREATE TABLE `ad` (
  `ad_id` int(11) NOT NULL,
  `title` varchar(10) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `start_time` datetime DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `ad`
--

INSERT INTO `ad` (`ad_id`, `title`, `image_path`, `link_url`, `is_active`, `start_time`, `end_time`) VALUES
(1, 'Razer周年慶', 'ad_img/Razer0318~0324周年慶.png', 'https://24h.pchome.com.tw/sites/razer?banner=new24_c7', 1, '2025-03-23 03:16:49', '2025-03-24 03:15:02');

-- --------------------------------------------------------

--
-- 資料表結構 `admin_action_log`
--

CREATE TABLE `admin_action_log` (
  `log_id` int(11) NOT NULL,
  `admin_id` varchar(20) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `target_id` varchar(100) DEFAULT NULL,
  `action_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `admin_login_log`
--

CREATE TABLE `admin_login_log` (
  `log_id` int(11) NOT NULL,
  `admin_id` varchar(20) DEFAULT NULL,
  `action` enum('login','logout') DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `brand`
--

INSERT INTO `brand` (`brand_id`, `name`) VALUES
(2, 'Logitech 羅技'),
(1, 'Razer 雷蛇');

-- --------------------------------------------------------

--
-- 資料表結構 `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` varchar(5) DEFAULT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `category`
--

CREATE TABLE `category` (
  `category_id` varchar(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
('MOUSE', '滑鼠'),
('EARPH', '耳機'),
('KEYBO', '鍵盤'),
('CHAIR', '電競椅');

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` varchar(5) DEFAULT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `status` varchar(10) DEFAULT NULL,
  `total_amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `product_id` varchar(10) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `category_id` varchar(5) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `detail_description` text DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `inserting_time` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `brand_id`, `image_path`, `short_description`, `detail_description`, `price`, `inserting_time`, `is_active`) VALUES
('EA00000001', 'Logitech 羅技 G733 無線RGB炫光電競耳麥(神秘黑)', 'EARPH', 2, 'img/Logitech 羅技 G733 無線RGB炫光電競耳麥(神秘黑).png', '•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n', '•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n', 3990, '2025-03-24 00:29:50', 1),
('KE00000001', '雷蛇 RAZER BLACKWIDOW V3 TENKEYLESS TKL', 'KEYBO', 1, 'img/RZ03-03490100-R3M1.png', '1、專為遊戲設計的Razer™機械軸\r\n2、8,000萬次按鍵敲擊使用壽命\r\n3、無數字鍵外型設計', '1、專為遊戲設計的Razer™機械軸\r\n2、8,000萬次按鍵敲擊使用壽命\r\n3、無數字鍵外型設計\r\n\r\n1、專為遊戲設計的Razer™機械軸\r\n2、8,000萬次按鍵敲擊使用壽命\r\n3、無數字鍵外型設計\r\n\r\n1、專為遊戲設計的Razer™機械軸\r\n2、8,000萬次按鍵敲擊使用壽命\r\n3、無數字鍵外型設計', 2090, '2025-03-23 04:13:31', 1),
('KE00000002', 'BlackWidow V3 Quartz', 'KEYBO', 1, 'img/BlackWidowV3Quartz.png', '•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n', '•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n', 2688, '2025-03-23 06:00:16', 1),
('KE00000003', 'DeathStalker V2 Pro TKL 無線機械式鍵盤(紅軸/英文)', 'KEYBO', 1, 'img/DeathStalker V2 Pro TKL.png', '•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n', '•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n', 3490, '2025-03-23 22:56:00', 1),
('KE00000004', 'BlackWidow 黑寡婦 V3 Mini蜘幻彩版無線機械式RGB英文鍵盤', 'KEYBO', 1, 'img/BlackWidow 黑寡婦 V3 Mini.png', '•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n\r\n', '•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n', 2990, '2025-03-23 22:58:04', 1),
('KE00000005', 'asdmfnoqf', 'KEYBO', NULL, 'img/BlackWidowV3Quartz', 'asdfjoiqhfoqfo', 'fjqpfpq', 2000, '2025-03-24 13:53:29', 1),
('MO00000001', 'Logitech 羅技M650 多工靜音無線滑鼠-珍珠白', 'MOUSE', 2, 'img/Logitech 羅技M650 多工靜音無線滑鼠-珍珠白.png\r\n', '•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區\r\n', '•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區', 1090, '2025-03-24 00:28:50', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `product_tag`
--

CREATE TABLE `product_tag` (
  `product_id` varchar(10) NOT NULL,
  `tag_id` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product_tag`
--

INSERT INTO `product_tag` (`product_id`, `tag_id`) VALUES
('EA00000001', 'US001'),
('EA00000001', 'US004'),
('KE00000001', 'KE001'),
('KE00000001', 'KE004'),
('KE00000001', 'US001'),
('KE00000001', 'US003'),
('KE00000002', 'KE001'),
('KE00000002', 'KE004'),
('KE00000002', 'US003'),
('KE00000002', 'US005'),
('KE00000003', 'KE001'),
('KE00000003', 'KE006'),
('KE00000003', 'US001'),
('KE00000003', 'US004'),
('KE00000003', 'US007'),
('KE00000004', 'KE001'),
('KE00000004', 'KE007'),
('KE00000004', 'US001'),
('KE00000004', 'US004'),
('MO00000001', 'US004'),
('MO00000001', 'US008');

-- --------------------------------------------------------

--
-- 資料表結構 `super_admin`
--

CREATE TABLE `super_admin` (
  `admin_id` varchar(20) NOT NULL,
  `admin_account` varchar(255) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `super_admin`
--

INSERT INTO `super_admin` (`admin_id`, `admin_account`, `admin_password`, `level`, `last_login`) VALUES
('承壎', 'asd07160821', 'asd08210716', 3, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `tag`
--

CREATE TABLE `tag` (
  `tag_id` varchar(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `tag_type_id` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `tag`
--

INSERT INTO `tag` (`tag_id`, `name`, `tag_type_id`) VALUES
('KE001', '機械', 'KE001'),
('KE002', '薄膜', 'KE001'),
('KE003', '光學', 'KE001'),
('KE004', '綠軸', 'KE002'),
('KE005', '青軸', 'KE002'),
('KE006', '紅軸', 'KE002'),
('KE007', '黃軸', 'KE002'),
('US001', '黑', 'US001'),
('US002', '紅', 'US001'),
('US003', '有線', 'US002'),
('US004', '無線', 'US002'),
('US005', '粉晶', 'US001'),
('US006', '雙模', 'US002'),
('US007', '三模', 'US002'),
('US008', '珍珠白', 'US001');

-- --------------------------------------------------------

--
-- 資料表結構 `tag_category`
--

CREATE TABLE `tag_category` (
  `tag_type_id` varchar(5) NOT NULL,
  `category_id` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `tag_category`
--

INSERT INTO `tag_category` (`tag_type_id`, `category_id`) VALUES
('KE001', 'KEYBO'),
('KE002', 'KEYBO'),
('US001', 'CHAIR'),
('US001', 'EARPH'),
('US001', 'KEYBO'),
('US001', 'MOUSE'),
('US002', 'CHAIR'),
('US002', 'EARPH'),
('US002', 'KEYBO'),
('US002', 'MOUSE');

-- --------------------------------------------------------

--
-- 資料表結構 `tag_type`
--

CREATE TABLE `tag_type` (
  `tag_type_id` varchar(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `tag_type`
--

INSERT INTO `tag_type` (`tag_type_id`, `name`) VALUES
('KE001', '觸發結構'),
('KE002', '軸體'),
('US002', '連接方式'),
('US001', '顏色');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `user_id` varchar(5) NOT NULL,
  `account` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `phone_numbers` varchar(10) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `ad`
--
ALTER TABLE `ad`
  ADD PRIMARY KEY (`ad_id`);

--
-- 資料表索引 `admin_action_log`
--
ALTER TABLE `admin_action_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 資料表索引 `admin_login_log`
--
ALTER TABLE `admin_login_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 資料表索引 `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- 資料表索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- 資料表索引 `product_tag`
--
ALTER TABLE `product_tag`
  ADD PRIMARY KEY (`product_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- 資料表索引 `super_admin`
--
ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- 資料表索引 `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `tag_type_id` (`tag_type_id`);

--
-- 資料表索引 `tag_category`
--
ALTER TABLE `tag_category`
  ADD PRIMARY KEY (`tag_type_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- 資料表索引 `tag_type`
--
ALTER TABLE `tag_type`
  ADD PRIMARY KEY (`tag_type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ad`
--
ALTER TABLE `ad`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin_action_log`
--
ALTER TABLE `admin_action_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin_login_log`
--
ALTER TABLE `admin_login_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `admin_action_log`
--
ALTER TABLE `admin_action_log`
  ADD CONSTRAINT `admin_action_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `super_admin` (`admin_id`);

--
-- 資料表的限制式 `admin_login_log`
--
ALTER TABLE `admin_login_log`
  ADD CONSTRAINT `admin_login_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `super_admin` (`admin_id`);

--
-- 資料表的限制式 `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- 資料表的限制式 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- 資料表的限制式 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- 資料表的限制式 `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`);

--
-- 資料表的限制式 `product_tag`
--
ALTER TABLE `product_tag`
  ADD CONSTRAINT `product_tag_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);

--
-- 資料表的限制式 `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `tag_ibfk_1` FOREIGN KEY (`tag_type_id`) REFERENCES `tag_type` (`tag_type_id`);

--
-- 資料表的限制式 `tag_category`
--
ALTER TABLE `tag_category`
  ADD CONSTRAINT `tag_category_ibfk_1` FOREIGN KEY (`tag_type_id`) REFERENCES `tag_type` (`tag_type_id`),
  ADD CONSTRAINT `tag_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
