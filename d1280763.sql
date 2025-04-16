-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-04-16 14:26:15
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
(1, 'Razer周年慶', 'ad_img/Razer0318~0324周年慶.png', 'https://24h.pchome.com.tw/sites/razer?banner=new24_c7', 1, '2025-03-23 03:16:49', '2025-03-29 03:15:00');

-- --------------------------------------------------------

--
-- 資料表結構 `admin_action_log`
--

CREATE TABLE `admin_action_log` (
  `log_id` int(11) NOT NULL,
  `admin_id` varchar(20) NOT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `target_id` varchar(100) DEFAULT NULL,
  `action_time` datetime DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `admin_action_log`
--

INSERT INTO `admin_action_log` (`log_id`, `admin_id`, `action_type`, `target_table`, `target_id`, `action_time`, `details`) VALUES
(58, '承壎', '新增', 'tag_type', 'US003', '2025-04-06 04:15:45', '新增標籤：測試（分類：滑鼠, 耳機, 鍵盤）'),
(59, '承壎', '更新', 'tag_type', 'US003', '2025-04-06 04:16:00', '更新標籤：測試\n分類變更：[耳機, 鍵盤, 滑鼠] → [滑鼠, 鍵盤]'),
(60, '承壎', '刪除', 'tag_type', 'US003', '2025-04-06 04:16:10', '刪除標籤：測試（分類：鍵盤, 滑鼠）'),
(61, '承壎', '刪除', 'brand', '3', '2025-04-06 04:24:58', '刪除 brand 資料：3'),
(62, '承壎', '新增', 'ad', '', '2025-04-06 04:40:24', '新增廣告 [ 測試 ] 狀態 : 1 , 開始時間 : 2025-04-08T04:40 , 結束時間 : \'2025-04-26T04:40\''),
(63, '承壎', '更新', 'product', NULL, '2025-04-06 04:40:53', '更新廣告 [ 測試 ] 狀態 : 0 , 開始時間 :  , 結束時間 : \'2025-04-26T04:40\''),
(64, '承壎', '更新', 'ad', '2', '2025-04-06 04:42:31', '更新廣告 [ 測試 ]\n狀態：0 → 1\n開始：2025-04-08 04:40:00 → \n結束：2025-04-26 04:40:00 → 2025-04-26T04:40'),
(65, '承壎', '更新', 'ad', '2', '2025-04-06 04:58:20', '更新廣告 [ 測試 ]\n狀態：1 → 0\n開始：0000-00-00 00:00:00 → \n結束：2025-04-26 04:40:00 → 2025-04-26T04:40'),
(66, '承壎', '刪除', 'ad', '2', '2025-04-06 04:58:43', '刪除廣告 [ 測試 ] 狀態 : 0 , 開始時間 : 0000-00-00 00:00:00 , 結束時間 : 2025-04-26 04:40:00'),
(67, '承壎', '新增', 'ad', '', '2025-04-06 04:59:13', '新增廣告 [ 測試 ] 狀態 : 1 , 開始時間 : 2025-04-08T04:58 , 結束時間 : \'2025-04-29T04:59\''),
(68, '承壎', '更新', 'ad', '3', '2025-04-06 04:59:36', '更新廣告 [ 測試 ]\n狀態：1 → 0\n開始：2025-04-08 04:58:00 → \n結束：2025-04-29 04:59:00 → 2025-04-25T04:59'),
(69, '承壎', '刪除', 'ad', '3', '2025-04-06 05:00:04', '刪除廣告 [ 測試 ] 狀態 : 0 , 開始時間 : 0000-00-00 00:00:00 , 結束時間 : 2025-04-25 04:59:00'),
(70, '承壎', '刪除', 'tag_type', 'US003', '2025-04-10 03:21:11', '刪除標籤：測試（分類：電競椅, 鍵盤）'),
(71, '承壎', '更新', 'tag', 'US011', '2025-04-10 04:14:06', '更新標籤細項：測試789 [789測試] → [測試789]'),
(72, '承壎', '刪除', 'tag', 'US011', '2025-04-10 04:14:22', '刪除標籤細項：測試789（標籤：測試789）'),
(73, '承壎', '刪除', 'tag_type', 'US004', '2025-04-10 04:14:28', '刪除標籤：測試789（分類：耳機, 鍵盤, 滑鼠）'),
(74, '承壎', '刪除', 'tag_type', 'US003', '2025-04-10 04:14:36', '刪除標籤：測試（分類：耳機, 鍵盤, 滑鼠）'),
(75, '承壎', '刪除', 'category', '分類??0', '2025-04-10 04:29:15', '刪除 category 資料：分類??0'),
(76, '承壎', '刪除', 'category', 'TESTT', '2025-04-10 04:29:24', '刪除 category 資料：TESTT'),
(77, '承壎', '刪除', 'brand', '4', '2025-04-10 04:30:14', '刪除 brand 資料：4'),
(78, '承壎', '刪除', 'category', 'TESTT', '2025-04-10 04:34:31', '刪除 category 資料：TESTT'),
(79, '承壎', '刪除', 'brand', '5', '2025-04-10 04:34:35', '刪除 brand 資料：5'),
(80, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 04:59:37', '刪除 分類測試 [123, 品牌測試]'),
(81, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 05:00:03', '刪除 分類測試 [123, 品牌測試]'),
(82, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 05:00:23', '刪除 分類測試 [123, 品牌測試]'),
(83, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 05:28:36', '刪除 分類測試 [123, 品牌測試]'),
(84, '承壎', '新增', 'product', 'CT00000002', '2025-04-10 05:58:58', '新增 分類測試 [123, 品牌測試]'),
(85, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 05:59:31', '刪除 分類測試 [123, 品牌測試]'),
(86, '承壎', '刪除', 'product', 'CT00000002', '2025-04-10 06:00:16', '刪除 分類測試 [123, 品牌測試]'),
(87, '承壎', '新增', 'product', 'CT00000001', '2025-04-10 06:01:18', '新增 分類測試 [測試1, 品牌測試]'),
(88, '承壎', '新增', 'product', 'CT00000002', '2025-04-10 06:01:18', '新增 分類測試 [測試2, 品牌測試]'),
(89, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 06:01:28', '刪除 分類測試 [測試1, 品牌測試]'),
(90, '承壎', '刪除', 'product', 'CT00000002', '2025-04-10 06:01:32', '刪除 分類測試 [測試2, 品牌測試]'),
(91, '承壎', '刪除', 'category', 'TTTTT', '2025-04-10 06:06:34', '刪除 category 資料：TTTTT'),
(93, '承壎', '刪除', 'category', 'CTESE', '2025-04-10 06:17:00', '刪除 category 資料：CTESE'),
(94, '承壎', '新增', 'category', 'TTTTT', '2025-04-10 06:17:13', '新增分類 [ 分類測試2 ]'),
(95, '承壎', '新增', 'brand', '測01', '2025-04-10 06:19:07', '新增品牌 [ 測試2號 ]'),
(96, '承壎', '刪除', 'category', 'TTTTT', '2025-04-10 06:19:14', '刪除 category 資料：TTTTT'),
(97, '承壎', '刪除', 'brand', '7', '2025-04-10 06:19:21', '刪除 brand 資料：7'),
(98, '承壎', '新增', 'tag_type', 'US003', '2025-04-10 06:23:23', '新增標籤：（分類：分類測試, 滑鼠, 耳機, 鍵盤, 電競椅）'),
(99, '承壎', '刪除', 'tag_type', 'US003', '2025-04-10 06:28:38', '刪除標籤：測試001（分類：分類測試, 滑鼠, 耳機, 鍵盤, 電競椅）'),
(100, '承壎', '新增', 'tag_type', 'CT002', '2025-04-10 06:29:05', '新增標籤類型：測試001（分類：分類測試）'),
(101, '承壎', '刪除', 'tag_type', 'CT002', '2025-04-10 06:29:35', '刪除標籤：測試001（分類：分類測試）'),
(102, '承壎', '新增', 'tag', 'CT002', '2025-04-10 06:32:01', '新增標籤細項：（標籤：類型測試）'),
(103, '承壎', '刪除', 'tag', 'CT002', '2025-04-10 06:32:41', '刪除標籤細項：標籤測試的測試（標籤：類型測試）'),
(104, '承壎', '新增', 'tag', 'CT002', '2025-04-10 06:32:52', '新增標籤細項：標籤測試的測試（標籤：類型測試）'),
(105, '承壎', '刪除', 'tag', 'CT002', '2025-04-10 06:33:35', '刪除標籤細項：標籤測試的測試（標籤：類型測試）'),
(106, '承壎', '刪除', 'tag_type', 'CT001', '2025-04-10 06:35:06', '刪除標籤：類型測試（分類：分類測試）'),
(107, '承壎', '刪除', 'category', 'CTEST', '2025-04-10 06:35:12', '刪除 category 資料：CTEST'),
(108, '承壎', '刪除', 'brand', '6', '2025-04-10 06:35:18', '刪除 brand 資料：6'),
(109, '承壎', '新增', 'category', 'CTEST', '2025-04-10 06:36:07', '新增分類 [ 測試 ]'),
(110, '承壎', '新增', 'brand', '測01', '2025-04-10 06:36:15', '新增品牌 [ 測試品牌 ]'),
(111, '承壎', '新增', 'tag_type', 'CT001', '2025-04-10 06:37:11', '新增標籤類型：標籤類型測試（分類：測試）'),
(112, '承壎', '新增', 'tag', 'CT001', '2025-04-10 06:37:30', '新增標籤細項：標籤細項測試（標籤類型：標籤類型測試）'),
(113, '承壎', '新增', 'product', 'CT00000001', '2025-04-10 06:37:49', '懶人-新增 測試 [123, 測試品牌]'),
(114, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 06:38:21', '刪除 測試 [123, 測試品牌]'),
(115, '承壎', '新增', 'product', 'CT00000001', '2025-04-10 06:38:45', '懶人-新增 測試 [123, 測試品牌]'),
(116, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 06:40:31', '刪除 測試 [123, 測試品牌]'),
(117, '承壎', '新增', 'product', 'CT00000001', '2025-04-10 06:40:50', '懶人-新增 測試 [123, 測試品牌]'),
(118, '承壎', '刪除', 'product', 'CT00000001', '2025-04-10 06:41:54', '刪除 測試 [123, 測試品牌]'),
(119, '承壎', '新增', 'product', 'CT00000001', '2025-04-10 06:42:49', '懶人-新增 測試 [123, 測試品牌]');

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

--
-- 傾印資料表的資料 `admin_login_log`
--

INSERT INTO `admin_login_log` (`log_id`, `admin_id`, `action`, `ip_address`, `timestamp`) VALUES
(1, '承壎', 'logout', '127.0.0.1', '2025-04-06 07:48:09'),
(2, '承壎', 'login', '127.0.0.1', '2025-04-06 07:53:33'),
(3, '承壎', 'logout', '127.0.0.1', '2025-04-06 08:20:11'),
(4, '承壎', 'login', '127.0.0.1', '2025-04-07 00:56:24'),
(5, '承壎', 'login', '127.0.0.1', '2025-04-09 18:58:23');

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
(1, 'Razer 雷蛇'),
(8, '測試品牌');

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
('CTEST', '測試'),
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
  `detail_description` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `inserting_time` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `brand_id`, `image_path`, `short_description`, `detail_description`, `price`, `inserting_time`, `is_active`, `stock_quantity`) VALUES
('CT00000001', '123', 'CTEST', 8, 'img/er_model.png', '短\r\n短\r\n短', '詳細\r\n詳細\r\n詳細', 123, '2025-04-10 00:42:49', 1, 1),
('EA00000001', 'Logitech 羅技 G733 無線RGB炫光電競耳麥(神秘黑)', 'EARPH', 2, 'img/Logitech 羅技 G733 無線RGB炫光電競耳麥(神秘黑).png', '•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n', '•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n•	LIGHTSPEED 無線傳輸技術\r\n•	輕盈舒適重量僅 278公克\r\n', 3990, '2025-03-24 00:29:50', 1, 1),
('KE00000001', '雷蛇 RAZER BLACKWIDOW V3 TENKEYLESS TKL', 'KEYBO', 1, 'img/RZ03-03490100-R3M1.png', '1、專為遊戲設計的Razer™機械軸2、8,000萬次按鍵敲擊使用壽命3、無數字鍵外型設計', '1、專為遊戲設計的Razer™機械軸2、8,000萬次按鍵敲擊使用壽命3、無數字鍵外型設計1、專為遊戲設計的Razer™機械軸2、8,000萬次按鍵敲擊使用壽命3、無數字鍵外型設計1、專為遊戲設計的Razer™機械軸2、8,000萬次按鍵敲擊使用壽命3、無數字鍵外型設計', 2090, '2025-03-23 04:13:31', 1, 1),
('KE00000002', 'BlackWidow V3 Quartz', 'KEYBO', 1, 'img/BlackWidowV3Quartz.png', '•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n', '•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n•	Razer™ 機械式綠軸\r\n•	雙射出成型ABS鍵帽\r\n•	內建記憶體外加雲端的混合式儲存\r\n', 2688, '2025-03-23 06:00:16', 1, 1),
('KE00000003', 'DeathStalker V2 Pro TKL 無線機械式鍵盤(紅軸/英文)', 'KEYBO', 1, 'img/DeathStalker V2 Pro TKL.png', '•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲', '•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n•	2.4G (HyperSpeed)、藍牙或 Type-C 連接\r\n•	美型輕薄 線性矮軸按鍵\r\n•	無右方數字九宮格鍵 精省右手操作空間 FPS遊戲適用\r\n', 3490, '2025-03-23 22:56:00', 1, 1),
('KE00000004', 'BlackWidow 黑寡婦 V3 Mini蜘幻彩版無線機械式RGB英文鍵盤', 'KEYBO', 1, 'img/BlackWidow 黑寡婦 V3 Mini.png', '•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n\r\n', '•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n•	特色：65% 大小的外型設計\r\n•	特色：RAZER機械軸-中文/黃軸\r\n•	有線/無線：藍牙三種連線模式\r\n', 2990, '2025-03-23 22:58:04', 1, 1),
('KE00000005', 'asdmfnoqf', 'KEYBO', 1, 'img/BlackWidowV3Quartz.png', 'asdfjoiqhfoqfo', '規格說明\r\n\r\n保固期限  2年\r\n\r\n保固種類  原廠保固\r\n\r\n顏色  黑色\r\n\r\n連接方式  有線\r\n\r\n適用於  遊戲電競\r\n\r\n供電方式  無\r\n\r\n人體工學  否\r\n\r\n觸發結構  機械\r\n\r\n軸體  其他\r\n\r\n鍵位尺寸  全尺寸(100%)\r\n', 2000, '2025-03-24 13:53:29', 1, 1),
('KE00000007', 'RAZER 雷蛇BlackWidow V4 75% 黑寡婦 V4 75%幻彩版機械式電競鍵盤(英文)', 'KEYBO', 2, 'img/RAZER雷蛇BlackWidowV475黑寡婦.png', '第 3 代 RAZER™ 觸感機械橘軸\r\n熱插拔設計\r\n精簡 75% 配置搭配鋁質上蓋', 'img/RAZER 雷蛇BlackWidow V4 75黑寡婦 V4 75幻彩版機械式電競鍵盤(英文)-1.jpg', 5190, '2025-04-04 20:40:27', 1, 1),
('MO00000001', 'Logitech 羅技M650 多工靜音無線滑鼠-珍珠白', 'MOUSE', 2, 'img/Logitech 羅技M650 多工靜音無線滑鼠-珍珠白.png\r\n', '•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區\r\n', '•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區•	簡約美學三色時尚自由選擇\r\n•	舒適合手流線外型配備橡膠握側條及柔軟拇指區', 1090, '2025-03-24 00:28:50', 1, 1);

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
('CT00000001', 'CT001'),
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
('KE00000007', 'KE001'),
('KE00000007', 'US001'),
('KE00000007', 'US003'),
('MO00000001', 'US004'),
('MO00000001', 'US008');

-- --------------------------------------------------------

--
-- 資料表結構 `stock_reservation`
--

CREATE TABLE `stock_reservation` (
  `reservation_id` int(11) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `reserved_quantity` int(11) NOT NULL,
  `reserved_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_committed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `super_admin`
--

CREATE TABLE `super_admin` (
  `admin_id` varchar(20) NOT NULL,
  `admin_account` varchar(255) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 停用，1 啟用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `super_admin`
--

INSERT INTO `super_admin` (`admin_id`, `admin_account`, `admin_password`, `level`, `last_login`, `is_active`) VALUES
('承壎', 'asd07160821', '$2y$10$jCDwsFwCKtpXu3v7fKDzcOoQMIxCt0rNxVx73Wq2dGctNEa/XDalO', 3, '2025-04-09 18:58:23', 1);

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
('CT001', '標籤細項測試', 'CT001'),
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
('CT001', 'CTEST'),
('KE001', 'KEYBO'),
('KE002', 'KEYBO'),
('US001', 'CHAIR'),
('US001', 'EARPH'),
('US001', 'KEYBO'),
('US001', 'MOUSE'),
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
('CT001', '標籤類型測試'),
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
  ADD KEY `fk_admin_action_log_admin` (`admin_id`);

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
-- 資料表索引 `stock_reservation`
--
ALTER TABLE `stock_reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

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
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin_action_log`
--
ALTER TABLE `admin_action_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin_login_log`
--
ALTER TABLE `admin_login_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- 使用資料表自動遞增(AUTO_INCREMENT) `stock_reservation`
--
ALTER TABLE `stock_reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `admin_action_log`
--
ALTER TABLE `admin_action_log`
  ADD CONSTRAINT `fk_admin_action_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `super_admin` (`admin_id`);

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
-- 資料表的限制式 `stock_reservation`
--
ALTER TABLE `stock_reservation`
  ADD CONSTRAINT `stock_reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `stock_reservation_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

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
