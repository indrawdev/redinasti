-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2014 at 02:53 AM
-- Server version: 5.6.11
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `redinast_nadera`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_auth_group`
--

CREATE TABLE IF NOT EXISTS `app_auth_group` (
  `id_auth_group` int(11) NOT NULL AUTO_INCREMENT,
  `auth_group` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_auth_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_auth_group`
--

TRUNCATE TABLE `app_auth_group`;
--
-- Dumping data for table `app_auth_group`
--

INSERT INTO `app_auth_group` (`id_auth_group`, `auth_group`, `is_superadmin`) VALUES
(1, 'Administrator', 1),
(2, 'Admin Divisi', 0),
(3, 'Admin Kasir', 1),
(4, 'Contoh Group', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_auth_menu`
--

CREATE TABLE IF NOT EXISTS `app_auth_menu` (
  `id_auth_menu` int(11) NOT NULL AUTO_INCREMENT,
  `parent_auth_menu` int(11) NOT NULL DEFAULT '0',
  `menu` varchar(255) COLLATE utf8_bin NOT NULL,
  `file` varchar(255) COLLATE utf8_bin NOT NULL,
  `position` tinyint(4) DEFAULT '1',
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_auth_menu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=41 ;

--
-- Truncate table before insert `app_auth_menu`
--

TRUNCATE TABLE `app_auth_menu`;
--
-- Dumping data for table `app_auth_menu`
--

INSERT INTO `app_auth_menu` (`id_auth_menu`, `parent_auth_menu`, `menu`, `file`, `position`, `is_superadmin`) VALUES
(1, 0, 'Admin Management', '#', 1, 1),
(2, 1, 'Admin User', 'admin', 1, 1),
(3, 1, 'Menu', 'menu', 1, 1),
(4, 1, 'Admin User Group &amp; Authorization', 'group', 1, 1),
(18, 1, 'Site Management', 'site', 2, 1),
(20, 0, 'Master Data', '#', 3, 0),
(21, 20, 'Supplier', 'supplier', 4, 0),
(22, 20, 'Divisi', 'division', 5, 0),
(23, 40, 'Bahan Baku &amp; Pembantu', 'product', 6, 0),
(24, 40, 'Kategori Bahan Baku &amp; Pembantu', 'category', 7, 0),
(25, 0, 'Transaksi', '#', 8, 0),
(26, 37, 'Bahan Baku &amp; Pembantu dari Supplier', 'supplier_purchase', 9, 0),
(27, 36, 'Bahan Baku &amp; Pembantu ke Divisi', 'division_purchase', 10, 0),
(29, 36, 'Barang Produksi', 'sales', 12, 0),
(30, 20, 'Toko', 'store', 13, 0),
(31, 0, 'Kasir', 'cashier', 14, 1),
(32, 37, 'Barang Produksi', 'purchase', 15, 0),
(33, 20, 'Barang', '#', 16, 0),
(34, 33, 'Nama Barang', 'item_category', 17, 0),
(35, 33, 'Kode Barang', 'item', 18, 0),
(36, 25, 'Penjualan', '#', 19, 0),
(37, 25, 'Pembelian', '#', 20, 0),
(38, 0, 'Report', 'report', 21, 0),
(39, 20, 'Giro', 'giro', 22, 0),
(40, 20, 'Bahan Baku &amp; Pembantu', '#', 23, 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_auth_menu_group`
--

CREATE TABLE IF NOT EXISTS `app_auth_menu_group` (
  `id_auth_menu_group` bigint(11) NOT NULL AUTO_INCREMENT,
  `id_auth_group` int(11) NOT NULL DEFAULT '0',
  `id_auth_menu` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_auth_menu_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=447 ;

--
-- Truncate table before insert `app_auth_menu_group`
--

TRUNCATE TABLE `app_auth_menu_group`;
--
-- Dumping data for table `app_auth_menu_group`
--

INSERT INTO `app_auth_menu_group` (`id_auth_menu_group`, `id_auth_group`, `id_auth_menu`) VALUES
(275, 3, 31),
(293, 4, 25),
(294, 4, 26),
(295, 4, 27),
(296, 4, 29),
(422, 1, 1),
(423, 1, 2),
(424, 1, 3),
(425, 1, 4),
(426, 1, 18),
(427, 1, 20),
(428, 1, 21),
(429, 1, 22),
(430, 1, 24),
(431, 1, 30),
(432, 1, 33),
(433, 1, 35),
(434, 1, 39),
(435, 1, 40),
(436, 1, 23),
(437, 1, 34),
(438, 1, 25),
(439, 1, 36),
(440, 1, 27),
(441, 1, 29),
(442, 1, 37),
(443, 1, 26),
(444, 1, 32),
(445, 1, 31),
(446, 1, 38);

-- --------------------------------------------------------

--
-- Table structure for table `app_auth_pages`
--

CREATE TABLE IF NOT EXISTS `app_auth_pages` (
  `id_auth_pages` bigint(11) NOT NULL AUTO_INCREMENT,
  `id_auth_user_group` tinyint(4) NOT NULL DEFAULT '0',
  `id_menu_admin` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_auth_pages`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_auth_pages`
--

TRUNCATE TABLE `app_auth_pages`;
-- --------------------------------------------------------

--
-- Table structure for table `app_auth_user`
--

CREATE TABLE IF NOT EXISTS `app_auth_user` (
  `id_auth_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_auth_group` int(11) NOT NULL,
  `id_site` int(11) NOT NULL DEFAULT '1',
  `id_division` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `userpass` text COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `image` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `alamat` text COLLATE utf8_bin,
  `organisasi` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `aktivasi` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_auth_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Truncate table before insert `app_auth_user`
--

TRUNCATE TABLE `app_auth_user`;
--
-- Dumping data for table `app_auth_user`
--

INSERT INTO `app_auth_user` (`id_auth_user`, `id_auth_group`, `id_site`, `id_division`, `username`, `userpass`, `name`, `email`, `image`, `alamat`, `organisasi`, `phone`, `modify_date`, `create_date`, `last_login`, `aktivasi`, `status`, `is_superadmin`) VALUES
(1, 1, 1, 1, 'admin', '_dca7bfaafe73bd78a4443e829703aa7a', 'Ivan Lubis', 'ivan@deptechdigital.com', 'us_ivan_lubis_7785ac3946389c7542b22c2714d8a1b8.jpg', '', NULL, '', '2014-07-13 10:19:23', '2014-01-02 10:58:55', '2014-01-02 17:58:55', NULL, 1, 1),
(2, 2, 1, 2, 'admin_divisi2', '_f7a8334110a9cb5edbc9a3592c7c401a', 'PIC Divisi 2', 'ivan.z.lubis@gmail.com', NULL, '', NULL, '', '2014-08-09 22:07:42', '2014-07-13 07:45:54', NULL, NULL, 1, 0),
(3, 2, 1, 3, 'admin_divisi1', '_4c9422347b85702d23faa4a3970c99a6', 'Admin Divisi 1', 'admin@divisi1.com', NULL, 'alamat', NULL, '081311124565', NULL, '2014-08-09 20:08:40', NULL, NULL, 1, 0),
(4, 3, 1, 1, 'kasir', '_3bf59ea031310473dbe66a0864100455', 'Kasir 1', 'kasir1@radena.com', NULL, '', NULL, '', NULL, '2014-08-23 01:30:14', NULL, NULL, 1, 1),
(5, 4, 1, 3, 'usercontoh', '_7a6797fb2f585336a2f7a773549945cd', 'contoh ', 'admin@divisi1d.com', NULL, '', NULL, '', NULL, '2014-08-23 03:51:15', NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_category`
--

CREATE TABLE IF NOT EXISTS `app_category` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(200) COLLATE utf8_bin NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_category`
--

TRUNCATE TABLE `app_category`;
--
-- Dumping data for table `app_category`
--

INSERT INTO `app_category` (`id_category`, `category`, `is_delete`, `create_date`) VALUES
(1, 'Kategori 1', 0, '2014-06-09 18:31:33'),
(2, 'Kategori 2', 0, '2014-06-09 18:31:49'),
(3, 'kategori 3', 0, '2014-08-16 10:01:12'),
(4, 'Finishing', 0, '2014-08-23 03:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `app_division`
--

CREATE TABLE IF NOT EXISTS `app_division` (
  `id_division` int(11) NOT NULL AUTO_INCREMENT,
  `division` varchar(200) COLLATE utf8_bin NOT NULL,
  `division_code` varchar(100) COLLATE utf8_bin NOT NULL,
  `division_pref` varchar(100) COLLATE utf8_bin NOT NULL,
  `division_pic` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `division_address` text COLLATE utf8_bin,
  `division_note` text COLLATE utf8_bin,
  `division_status` datetime DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `modify_date` datetime DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_division`
--

TRUNCATE TABLE `app_division`;
--
-- Dumping data for table `app_division`
--

INSERT INTO `app_division` (`id_division`, `division`, `division_code`, `division_pref`, `division_pic`, `division_address`, `division_note`, `division_status`, `is_delete`, `modify_date`, `create_date`) VALUES
(1, 'Divisi Admin', 'ADM', 'ADM', 'Anukan', 'ini alamat admin', 'catatan untuk admin', NULL, 0, NULL, '2014-06-05 18:20:52'),
(2, 'Divisi 2', 'DIV2', 'DIV2', 'Aku', 'alamat divisi 2', '', NULL, 0, NULL, '2014-08-09 16:09:28'),
(3, 'Divisi 1', 'DIV1', 'DIV1', 'Apic divisi 1', 'alamat div 1', '', NULL, 0, NULL, '2014-08-09 19:58:54'),
(4, 'Divisi CBR', 'CBR', 'CBR', 'Ferry', 'jalan cipicung', '', NULL, 0, NULL, '2014-09-13 04:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `app_division_credit`
--

CREATE TABLE IF NOT EXISTS `app_division_credit` (
  `id_division_credit` int(11) NOT NULL AUTO_INCREMENT,
  `id_division_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `credit_price` decimal(10,0) NOT NULL,
  `credit_status` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division_credit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_division_credit`
--

TRUNCATE TABLE `app_division_credit`;
-- --------------------------------------------------------

--
-- Table structure for table `app_division_product`
--

CREATE TABLE IF NOT EXISTS `app_division_product` (
  `id_division_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_division` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `division_product_stock` int(11) NOT NULL,
  `division_product_retur` int(11) NOT NULL,
  `division_product_price` decimal(11,0) NOT NULL,
  `division_product_status` tinyint(1) NOT NULL DEFAULT '0',
  `modify_date` datetime DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Truncate table before insert `app_division_product`
--

TRUNCATE TABLE `app_division_product`;
--
-- Dumping data for table `app_division_product`
--

INSERT INTO `app_division_product` (`id_division_product`, `id_division`, `id_product`, `division_product_stock`, `division_product_retur`, `division_product_price`, `division_product_status`, `modify_date`, `create_date`) VALUES
(1, 3, 2, 3, 1, '162068', 0, NULL, '2014-09-03 08:52:54'),
(2, 3, 4, 16, 4, '6064', 0, NULL, '2014-09-03 08:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `app_division_purchase`
--

CREATE TABLE IF NOT EXISTS `app_division_purchase` (
  `id_division_purchase` int(11) NOT NULL AUTO_INCREMENT,
  `id_division` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `id_auth_user` int(11) NOT NULL,
  `shipping_date` date DEFAULT NULL,
  `purchase_pic` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `purchase_note` text COLLATE utf8_bin,
  `payment_status` tinyint(1) NOT NULL COMMENT '0=transaksi baru; 1=sudah dibayar tapi belum lunas; 2=lunas',
  `total_price` decimal(10,0) NOT NULL,
  `total_price_retur` decimal(10,0) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division_purchase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `app_division_purchase`
--

TRUNCATE TABLE `app_division_purchase`;
--
-- Dumping data for table `app_division_purchase`
--

INSERT INTO `app_division_purchase` (`id_division_purchase`, `id_division`, `purchase_invoice`, `id_auth_user`, `shipping_date`, `purchase_pic`, `purchase_note`, `payment_status`, `total_price`, `total_price_retur`, `is_delete`, `create_date`) VALUES
(1, 3, 'INV3/1/20140903', 0, '2014-09-03', 'pic divisi 1', '', 1, '969552', '186324', 0, '2014-09-03 08:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `app_division_purchase_credit`
--

CREATE TABLE IF NOT EXISTS `app_division_purchase_credit` (
  `id_division_purchase_credit` int(11) NOT NULL AUTO_INCREMENT,
  `id_division_purchase` int(11) DEFAULT NULL,
  `id_division` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `credit_price` decimal(10,0) NOT NULL,
  `credit_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_division_purchase_credit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `app_division_purchase_credit`
--

TRUNCATE TABLE `app_division_purchase_credit`;
--
-- Dumping data for table `app_division_purchase_credit`
--

INSERT INTO `app_division_purchase_credit` (`id_division_purchase_credit`, `id_division_purchase`, `id_division`, `purchase_invoice`, `credit_price`, `credit_status`) VALUES
(1, 1, 3, 'INV3/1/20140903', '200000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_division_purchase_payment`
--

CREATE TABLE IF NOT EXISTS `app_division_purchase_payment` (
  `id_division_purchase_payment` int(11) NOT NULL AUTO_INCREMENT,
  `id_division_purchase` int(11) NOT NULL,
  `id_giro` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `payment_type` int(11) DEFAULT NULL COMMENT '1=cash;2=giro',
  `payment_note` text COLLATE utf8_bin,
  `payment_date` date DEFAULT NULL,
  `payment_total` decimal(10,0) DEFAULT NULL,
  `payment_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division_purchase_payment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `app_division_purchase_payment`
--

TRUNCATE TABLE `app_division_purchase_payment`;
--
-- Dumping data for table `app_division_purchase_payment`
--

INSERT INTO `app_division_purchase_payment` (`id_division_purchase_payment`, `id_division_purchase`, `id_giro`, `purchase_invoice`, `payment_type`, `payment_note`, `payment_date`, `payment_total`, `payment_image`, `create_date`) VALUES
(1, 1, 0, 'INV3/1/20140903', 1, 'Pengurangan dari retur barang.', '2014-09-11', '186324', NULL, '2014-09-11 03:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `app_division_purchase_product`
--

CREATE TABLE IF NOT EXISTS `app_division_purchase_product` (
  `id_division_purchase_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_division_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `purchase_qty` int(11) NOT NULL,
  `purchase_price` decimal(10,0) NOT NULL,
  `purchase_buy` decimal(10,0) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division_purchase_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Truncate table before insert `app_division_purchase_product`
--

TRUNCATE TABLE `app_division_purchase_product`;
--
-- Dumping data for table `app_division_purchase_product`
--

INSERT INTO `app_division_purchase_product` (`id_division_purchase_product`, `id_division_purchase`, `id_division`, `id_product`, `purchase_qty`, `purchase_price`, `purchase_buy`, `create_date`) VALUES
(1, 1, 3, 2, 4, '162068', '154350', '2014-09-03 15:52:54'),
(2, 1, 3, 4, 20, '6064', '5775', '2014-09-03 15:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `app_division_purchase_retur`
--

CREATE TABLE IF NOT EXISTS `app_division_purchase_retur` (
  `id_division_purchase_retur` int(11) NOT NULL AUTO_INCREMENT,
  `id_division_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `retur_qty` int(11) NOT NULL,
  `retur_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_division_purchase_retur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Truncate table before insert `app_division_purchase_retur`
--

TRUNCATE TABLE `app_division_purchase_retur`;
--
-- Dumping data for table `app_division_purchase_retur`
--

INSERT INTO `app_division_purchase_retur` (`id_division_purchase_retur`, `id_division_purchase`, `id_division`, `id_product`, `retur_qty`, `retur_price`) VALUES
(1, 1, 3, 2, 1, '162068'),
(2, 1, 3, 4, 4, '6064');

-- --------------------------------------------------------

--
-- Table structure for table `app_giro`
--

CREATE TABLE IF NOT EXISTS `app_giro` (
  `id_giro` int(11) NOT NULL AUTO_INCREMENT,
  `giro_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `giro_date` date NOT NULL,
  `giro_bank` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `giro_price` decimal(10,0) NOT NULL,
  `giro_from` tinyint(4) DEFAULT '0' COMMENT '1=Toko;2=Divisi',
  `giro_invoice` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `giro_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=un-use;1=used;2=cashed;',
  PRIMARY KEY (`id_giro`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Truncate table before insert `app_giro`
--

TRUNCATE TABLE `app_giro`;
--
-- Dumping data for table `app_giro`
--

INSERT INTO `app_giro` (`id_giro`, `giro_code`, `giro_date`, `giro_bank`, `giro_price`, `giro_from`, `giro_invoice`, `giro_status`) VALUES
(1, 'IAHDKJAD', '0000-00-00', 'bank mandiri', '2000000', 0, NULL, 0),
(2, 'AI7839982', '0000-00-00', 'BANK BCA', '4000000', 0, NULL, 1),
(3, 'QWQR8836', '0000-00-00', 'HSBC', '6000000', 0, NULL, 1),
(4, '5387987', '0000-00-00', 'BANK BNI', '10000000', 0, NULL, 0),
(7, 'OUDAH', '2014-09-09', 'Bank BCA', '2000000', 1, 'INVSLS/3/120140909', 2);

-- --------------------------------------------------------

--
-- Table structure for table `app_giro_cashed`
--

CREATE TABLE IF NOT EXISTS `app_giro_cashed` (
  `id_giro_cashed` int(11) NOT NULL AUTO_INCREMENT,
  `id_giro` int(11) NOT NULL,
  `giro_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `giro_price` decimal(10,0) NOT NULL,
  `note` text COLLATE utf8_bin,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_giro_cashed`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `app_giro_cashed`
--

TRUNCATE TABLE `app_giro_cashed`;
--
-- Dumping data for table `app_giro_cashed`
--

INSERT INTO `app_giro_cashed` (`id_giro_cashed`, `id_giro`, `giro_code`, `giro_price`, `note`, `create_date`) VALUES
(1, 7, 'OUDAH', '2000000', NULL, '2014-09-19 20:34:09');

-- --------------------------------------------------------

--
-- Table structure for table `app_item`
--

CREATE TABLE IF NOT EXISTS `app_item` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_item_category` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `item_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `item_name` varchar(200) COLLATE utf8_bin NOT NULL,
  `size` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `item_stock` int(11) NOT NULL DEFAULT '0',
  `item_stock_retur` int(11) NOT NULL,
  `item_hpp_price` decimal(11,0) NOT NULL,
  `item_sell_price` decimal(11,0) NOT NULL,
  `item_discount_price` decimal(10,0) DEFAULT NULL,
  `item_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=jadi;2=1/2 jadi;3=mentah',
  `item_note` text COLLATE utf8_bin,
  `item_status` tinyint(1) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Truncate table before insert `app_item`
--

TRUNCATE TABLE `app_item`;
--
-- Dumping data for table `app_item`
--

INSERT INTO `app_item` (`id_item`, `id_item_category`, `id_division`, `item_code`, `item_name`, `size`, `item_stock`, `item_stock_retur`, `item_hpp_price`, `item_sell_price`, `item_discount_price`, `item_type`, `item_note`, `item_status`, `is_delete`, `create_date`) VALUES
(1, 2, 3, 'DIV13-1-1-Carla', 'DIV13-1-1-Carla', '221', 4, 1, '765000', '1000000', NULL, 1, '', 0, 0, '2014-09-07 14:14:44'),
(2, 1, 3, 'DIV13-1-2-Eterna', 'DIV13-1-2-Eterna', '321', 1, 0, '1560000', '2000000', NULL, 1, '', 0, 0, '2014-09-09 06:06:36'),
(3, 3, 2, 'DIV22-1-3-Vario', 'DIV22-1-3-Vario', '221', 1, 1, '565000', '580000', NULL, 1, '', 0, 0, '2014-09-11 18:35:20'),
(4, 7, 3, 'DIV13-1-4-Avanza', 'DIV13-1-4-Avanza', '221', 4, 0, '630000', '700000', NULL, 1, '', 0, 0, '2014-09-11 18:40:10'),
(5, 8, 4, 'CBR4-1-5-Louis', 'CBR4-1-5-Louis', '221', 9, 0, '1115000', '1200000', NULL, 1, '', 0, 0, '2014-09-13 05:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `app_item_category`
--

CREATE TABLE IF NOT EXISTS `app_item_category` (
  `id_item_category` int(11) NOT NULL AUTO_INCREMENT,
  `item_category` varchar(200) COLLATE utf8_bin NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_item_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Truncate table before insert `app_item_category`
--

TRUNCATE TABLE `app_item_category`;
--
-- Dumping data for table `app_item_category`
--

INSERT INTO `app_item_category` (`id_item_category`, `item_category`, `is_delete`) VALUES
(1, 'Eterna', 0),
(2, 'Carla', 0),
(3, 'Vario', 0),
(4, 'Female', 0),
(5, 'Marbela', 0),
(6, 'Female Box', 0),
(7, 'Avanza', 0),
(8, 'Louis', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_localization`
--

CREATE TABLE IF NOT EXISTS `app_localization` (
  `id_localization` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(150) COLLATE utf8_bin NOT NULL,
  `iso_1` varchar(50) COLLATE utf8_bin NOT NULL,
  `iso_2` varchar(50) COLLATE utf8_bin NOT NULL,
  `locale_path` varchar(200) COLLATE utf8_bin NOT NULL,
  `locale_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_localization`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Truncate table before insert `app_localization`
--

TRUNCATE TABLE `app_localization`;
--
-- Dumping data for table `app_localization`
--

INSERT INTO `app_localization` (`id_localization`, `locale`, `iso_1`, `iso_2`, `locale_path`, `locale_status`) VALUES
(1, 'english', 'en', 'eng', 'english', 0),
(2, 'indonesia', 'id', 'ina', 'indonesia', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_logs`
--

CREATE TABLE IF NOT EXISTS `app_logs` (
  `id_logs` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(15) NOT NULL DEFAULT '0',
  `id_group` bigint(15) NOT NULL DEFAULT '0',
  `action` varchar(255) COLLATE utf8_bin NOT NULL,
  `desc` text CHARACTER SET utf8,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_logs`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=140 ;

--
-- Truncate table before insert `app_logs`
--

TRUNCATE TABLE `app_logs`;
--
-- Dumping data for table `app_logs`
--

INSERT INTO `app_logs` (`id_logs`, `id_user`, `id_group`, `action`, `desc`, `create_date`) VALUES
(1, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-02 10:59:32'),
(2, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-03 03:52:03'),
(3, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-03 09:49:03'),
(4, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-03 12:18:04'),
(5, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-06 03:51:35'),
(6, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-07 03:59:14'),
(7, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-08 05:12:51'),
(8, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-08 08:01:58'),
(9, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-09 09:22:06'),
(10, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-10 08:20:17'),
(11, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-15 04:16:48'),
(12, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-15 09:32:44'),
(13, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-16 05:04:35'),
(14, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin;', '2014-01-16 07:45:03'),
(15, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-16 07:45:11'),
(16, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-16 07:47:42'),
(17, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-17 04:08:03'),
(18, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-18 13:39:56'),
(19, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-18 17:50:55'),
(20, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 07:08:46'),
(21, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 14:07:36'),
(22, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 14:14:00'),
(23, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 14:15:01'),
(24, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 14:15:26'),
(25, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-19 16:00:11'),
(26, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-20 05:50:26'),
(27, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-20 10:37:53'),
(28, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-21 05:16:24'),
(29, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-22 03:27:22'),
(30, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-22 11:47:50'),
(31, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-23 05:53:47'),
(32, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-24 09:46:19'),
(33, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-26 15:45:46'),
(34, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-27 04:42:13'),
(35, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-27 09:18:48'),
(36, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-29 04:32:01'),
(37, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-29 12:18:27'),
(38, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-30 05:09:12'),
(39, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-01-30 07:52:31'),
(40, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-04 08:23:57'),
(41, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-09 15:31:32'),
(42, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-20 03:46:31'),
(43, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-20 04:00:31'),
(44, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-20 07:10:38'),
(45, 0, 0, 'Login', 'Login:failed; IP:::1; username:eiger_admin;', '2014-02-20 07:12:06'),
(46, 0, 0, 'Login', 'Login:failed; IP:::1; username:eiger_admin;', '2014-02-20 07:12:18'),
(47, 2, 1, 'Login', 'Login:succeed; IP:::1; username:eiger_admin;', '2014-02-20 07:13:00'),
(48, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-20 10:35:45'),
(49, 2, 1, 'Login', 'Login:succeed; IP:::1; username:eiger_admin;', '2014-02-20 10:36:28'),
(50, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-21 08:09:40'),
(51, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-21 11:12:44'),
(52, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-02-25 03:06:15'),
(53, 1, 1, 'Login', 'Login:succeed; IP:192.168.0.122; username:admin;', '2014-03-11 08:26:15'),
(54, 0, 0, 'Login', 'Login:failed; IP:192.168.0.122; username:eiger_admin;', '2014-03-11 08:29:22'),
(55, 0, 0, 'Login', 'Login:failed; IP:192.168.0.122; username:eiger_admin;', '2014-03-11 08:29:29'),
(56, 2, 1, 'Login', 'Login:succeed; IP:192.168.0.122; username:eiger_admin;', '2014-03-11 08:29:55'),
(57, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-04-23 06:47:50'),
(58, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-04-24 09:38:58'),
(59, 1, 1, 'Login', 'Login:succeed; IP:192.168.0.125; username:admin;', '2014-04-24 09:42:09'),
(60, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin;', '2014-05-30 11:18:18'),
(61, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin;', '2014-05-30 13:12:46'),
(62, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin;', '2014-05-30 13:12:52'),
(63, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-05-30 13:14:07'),
(64, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-05-31 16:31:49'),
(65, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-01 02:20:10'),
(66, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-05 16:27:53'),
(67, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-09 17:21:30'),
(68, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-21 01:13:00'),
(69, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-21 05:08:47'),
(70, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-23 14:48:26'),
(71, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-24 03:54:03'),
(72, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-24 09:22:37'),
(73, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-24 15:35:55'),
(74, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-25 03:18:28'),
(75, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-25 08:56:33'),
(76, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-25 11:45:48'),
(77, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-06-30 04:32:59'),
(78, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-01 01:00:42'),
(79, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-04 17:56:52'),
(80, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-05 15:50:36'),
(81, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-05 19:53:37'),
(82, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-06 05:25:16'),
(83, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-13 06:30:36'),
(84, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin_div;', '2014-07-13 07:47:27'),
(85, 2, 2, 'Login', 'Login:succeed; IP:::1; username:radena_div;', '2014-07-13 07:47:44'),
(86, 2, 2, 'Login', 'Login:succeed; IP:::1; username:radena_div;', '2014-07-13 08:16:38'),
(87, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-13 08:18:18'),
(88, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-13 08:19:39'),
(89, 2, 2, 'Login', 'Login:succeed; IP:::1; username:radena_div;', '2014-07-13 08:19:50'),
(90, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-13 21:46:21'),
(91, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-14 03:20:44'),
(92, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-14 06:03:05'),
(93, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-07-25 22:29:00'),
(94, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-02 15:28:58'),
(95, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-02 23:40:44'),
(96, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-03 02:06:34'),
(97, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-09 16:07:50'),
(98, 2, 2, 'Login', 'Login:succeed; IP:::1; username:admin_divisi;', '2014-08-09 17:57:35'),
(99, 3, 2, 'Login', 'Login:succeed; IP:::1; username:admin_divisi1;', '2014-08-09 20:09:19'),
(100, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-14 04:39:53'),
(101, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-14 18:26:49'),
(102, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-15 00:20:33'),
(103, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-15 20:35:08'),
(104, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-16 09:55:58'),
(105, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-17 07:05:44'),
(106, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-20 22:00:48'),
(107, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-22 11:07:44'),
(108, 0, 0, 'Login', 'Login:failed; IP:::1; username:admin;', '2014-08-23 01:28:02'),
(109, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-23 01:28:08'),
(110, 5, 4, 'Login', 'Login:succeed; IP:::1; username:usercontoh;', '2014-08-23 03:51:38'),
(111, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-31 04:36:03'),
(112, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-31 13:58:11'),
(113, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-08-31 17:58:35'),
(114, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-01 04:36:11'),
(115, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-01 07:27:38'),
(116, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-03 04:33:12'),
(117, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-03 15:52:30'),
(118, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-04 06:21:00'),
(119, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-06 02:51:37'),
(120, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-06 07:25:02'),
(121, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-07 07:49:36'),
(122, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-07 11:46:00'),
(123, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-09 04:16:20'),
(124, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-10 00:46:49'),
(125, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-10 14:14:16'),
(126, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-10 16:17:44'),
(127, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-11 02:22:34'),
(128, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-11 06:17:52'),
(129, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-11 13:00:33'),
(130, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-11 17:01:30'),
(131, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-12 03:55:41'),
(132, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-12 09:17:16'),
(133, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-12 21:49:45'),
(134, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-13 03:53:19'),
(135, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-14 08:27:50'),
(136, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-14 19:51:16'),
(137, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-15 06:54:28'),
(138, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-19 17:07:16'),
(139, 1, 1, 'Login', 'Login:succeed; IP:::1; username:admin;', '2014-09-20 04:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `app_product`
--

CREATE TABLE IF NOT EXISTS `app_product` (
  `id_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `product_code` varchar(100) COLLATE utf8_bin NOT NULL,
  `product_name` varchar(250) COLLATE utf8_bin NOT NULL,
  `product_pref` varchar(100) COLLATE utf8_bin NOT NULL,
  `product_unit` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `product_note` text COLLATE utf8_bin NOT NULL,
  `thumbnail_image` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `primary_image` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `buy_price` decimal(10,0) NOT NULL,
  `sell_price` decimal(10,0) NOT NULL,
  `product_status` tinyint(1) NOT NULL,
  `product_stock` int(11) NOT NULL,
  `product_retur` int(11) NOT NULL,
  `product_minimum` int(11) NOT NULL DEFAULT '1',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

--
-- Truncate table before insert `app_product`
--

TRUNCATE TABLE `app_product`;
--
-- Dumping data for table `app_product`
--

INSERT INTO `app_product` (`id_product`, `id_category`, `product_code`, `product_name`, `product_pref`, `product_unit`, `product_note`, `thumbnail_image`, `primary_image`, `buy_price`, `sell_price`, `product_status`, `product_stock`, `product_retur`, `product_minimum`, `is_delete`, `create_date`) VALUES
(1, 2, 'SKR634', 'Sekrup 6x3/4', '', 'KTG', '', 'thumb_sekrup_6x34-skr634_82926765510f1b0755895781668c1bbe.jpg', 'prod_sekrup_6x34-skr634_82926765510f1b0755895781668c1bbe.jpg', '48000', '50400', 0, 0, 0, 1, 0, '2014-06-09 20:04:39'),
(2, 1, 'AirUnitSingle', 'Air Unit Single', '', 'PCS', '', NULL, NULL, '154350', '162068', 0, 19, 3, 1, 0, '2014-07-05 20:16:28'),
(3, 1, 'Autolux', 'Autolux', '', 'KLG', '', NULL, NULL, '79050', '83003', 0, 0, 0, 4, 0, '2014-08-09 19:00:36'),
(4, 1, 'Bearing607', 'Bearing 607', '', 'PCS', '', NULL, NULL, '5775', '6064', 0, 69, 15, 2, 0, '2014-08-09 19:01:18'),
(5, 1, 'Coller', 'Coller', '', 'PCS', '', NULL, NULL, '26250', '27563', 0, 50, 0, 1, 0, '2014-08-09 19:02:27'),
(6, 1, 'JepitKaca', 'Jepit Kaca', '', 'KTG', '', NULL, NULL, '21000', '22050', 0, 5, 0, 0, 0, '2014-08-09 19:03:16'),
(7, 1, 'KainSalur', 'Kain Salur', '', 'MTR', '', NULL, NULL, '23100', '24255', 0, 70, 5, 1, 0, '2014-08-09 19:04:32'),
(8, 1, 'KarungBagor', 'Karung Bagor', '', 'ROL', '', NULL, NULL, '278250', '292163', 0, 4, 0, 1, 0, '2014-08-09 19:05:24'),
(9, 1, 'KunciSMW', 'Kunci SMW', '', 'DUS', '', NULL, NULL, '34656', '36389', 0, 0, 0, 1, 0, '2014-08-09 19:07:57'),
(10, 1, 'PakuLis', 'Paku Lis', '', 'KG', '', NULL, NULL, '14438', '15160', 0, 100, 0, 1, 0, '2014-08-09 19:18:37'),
(11, 3, 'test', 'test produk', '', 'PCS', '', NULL, NULL, '11000', '20000', 0, 0, 0, 5, 0, '2014-08-16 10:04:55'),
(12, 4, 'WF', 'Wood Fillar', '', 'KLG', '', NULL, NULL, '290000', '304500', 0, 20, 0, 5, 0, '2014-08-23 04:01:33'),
(13, 1, 'cat-thinner', 'Cat Thinner', '', 'LITER', 'ini dikonversi dari drum menjadi kaleng\r\n1 drum = 100 liter', NULL, NULL, '35000', '36750', 0, 400, 0, 50, 0, '2014-09-06 02:54:29'),
(14, 4, 'Colour Satin', 'Colour Satin Pujangga', '', 'PAIL', '', NULL, '', '325000', '350000', 0, 30, 0, 10, 0, '2014-09-13 04:05:52'),
(15, 4, 'Thinner HG', 'Thinner HG Arti', '', 'LITER', '1 drum = 200 liter', NULL, NULL, '8000', '8400', 0, 2400, 0, 1000, 0, '2014-09-13 04:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `app_production`
--

CREATE TABLE IF NOT EXISTS `app_production` (
  `id_production` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `production_stock` int(11) NOT NULL DEFAULT '0',
  `production_stock_retur` int(11) NOT NULL,
  `production_hpp_price` decimal(11,0) NOT NULL,
  `production_sell_price` decimal(11,0) NOT NULL,
  `production_discount_price` decimal(10,0) DEFAULT NULL,
  `production_note` text COLLATE utf8_bin,
  `production_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=default;1=;2=sold to store;3=retur to division;4=retur from store;',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_production`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Truncate table before insert `app_production`
--

TRUNCATE TABLE `app_production`;
--
-- Dumping data for table `app_production`
--

INSERT INTO `app_production` (`id_production`, `id_item`, `production_code`, `production_stock`, `production_stock_retur`, `production_hpp_price`, `production_sell_price`, `production_discount_price`, `production_note`, `production_status`, `is_delete`, `create_date`) VALUES
(1, 1, '10/412', 1, 1, '765000', '1000000', '0', NULL, 3, 0, '2014-09-09 06:10:20'),
(2, 2, '1/211', 1, 0, '1560000', '2000000', '0', NULL, 2, 0, '2014-09-09 06:10:20'),
(3, 4, '3/359', 1, 0, '630000', '700000', '0', NULL, 2, 0, '2014-09-11 18:42:06'),
(4, 4, '5/320', 1, 0, '630000', '700000', '0', NULL, 2, 0, '2014-09-11 18:50:51'),
(5, 4, '5/321', 1, 0, '630000', '700000', '0', NULL, 2, 0, '2014-09-11 18:50:51'),
(6, 1, '10/411', 1, 0, '765000', '1200000', '0', NULL, 2, 0, '2014-09-11 18:50:51'),
(7, 3, '3/232', 1, 1, '565000', '580000', '0', NULL, 3, 0, '2014-09-11 18:52:36'),
(8, 3, '3/230', 1, 0, '565000', '580000', '0', NULL, 2, 0, '2014-09-11 18:52:36'),
(9, 2, 'WS/1', 1, 0, '1560000', '2000000', '0', NULL, 0, 0, '2014-09-13 05:43:29'),
(10, 5, 'CBR/0102', 1, 0, '1115000', '1200000', '0', NULL, 2, 0, '2014-09-13 05:45:19');

-- --------------------------------------------------------

--
-- Table structure for table `app_production_bak2`
--

CREATE TABLE IF NOT EXISTS `app_production_bak2` (
  `id_production` int(11) NOT NULL AUTO_INCREMENT,
  `production_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `production_name` varchar(200) COLLATE utf8_bin NOT NULL,
  `size` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `production_stock` int(11) NOT NULL DEFAULT '0',
  `production_hpp_price` decimal(11,0) NOT NULL,
  `production_sell_price` decimal(11,0) NOT NULL,
  `production_discount_price` decimal(10,0) DEFAULT NULL,
  `production_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=jadi;2=1/2 jadi;3=mentah',
  `production_note` text COLLATE utf8_bin,
  `production_status` tinyint(1) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_production`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_production_bak2`
--

TRUNCATE TABLE `app_production_bak2`;
--
-- Dumping data for table `app_production_bak2`
--

INSERT INTO `app_production_bak2` (`id_production`, `production_code`, `production_name`, `size`, `production_stock`, `production_hpp_price`, `production_sell_price`, `production_discount_price`, `production_type`, `production_note`, `production_status`, `is_delete`, `create_date`) VALUES
(1, '5/320', 'Euro', NULL, 1, '180000', '200000', '0', 1, NULL, 2, 0, '2014-09-03 18:41:15'),
(2, '5/321', 'Natuzi', NULL, 1, '180000', '220000', '0', 1, NULL, 2, 0, '2014-09-03 18:41:15'),
(3, '5/79', 'Meja Makan', NULL, 1, '200000', '330000', '50000', 1, NULL, 0, 0, '2014-09-04 06:41:57'),
(4, '5/89', 'Inul', NULL, 1, '165000', '250000', '0', 1, NULL, 0, 0, '2014-09-04 06:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `app_production_category`
--

CREATE TABLE IF NOT EXISTS `app_production_category` (
  `id_production_category` int(11) NOT NULL AUTO_INCREMENT,
  `production_category` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_production_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_production_category`
--

TRUNCATE TABLE `app_production_category`;
-- --------------------------------------------------------

--
-- Table structure for table `app_production_cost`
--

CREATE TABLE IF NOT EXISTS `app_production_cost` (
  `id_production_cost` int(11) NOT NULL AUTO_INCREMENT,
  `id_production` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `production_code` varchar(255) COLLATE utf8_bin NOT NULL,
  `production_cost_note` text COLLATE utf8_bin,
  `production_cost` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_production_cost`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_production_cost`
--

TRUNCATE TABLE `app_production_cost`;
-- --------------------------------------------------------

--
-- Table structure for table `app_production_later`
--

CREATE TABLE IF NOT EXISTS `app_production_later` (
  `id_production` int(11) NOT NULL AUTO_INCREMENT,
  `id_division` int(11) NOT NULL,
  `id_production_category` int(11) NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `production_name` varchar(200) COLLATE utf8_bin NOT NULL,
  `size` varchar(100) COLLATE utf8_bin NOT NULL,
  `production_hpp_price` decimal(11,0) NOT NULL,
  `production_sell_price` decimal(11,0) NOT NULL,
  `production_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=jadi;2=1/2 jadi;3=mentah',
  `production_note` text COLLATE utf8_bin,
  `production_status` tinyint(1) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_production`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_production_later`
--

TRUNCATE TABLE `app_production_later`;
-- --------------------------------------------------------

--
-- Table structure for table `app_production_product`
--

CREATE TABLE IF NOT EXISTS `app_production_product` (
  `id_production_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `id_production` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `production_code` varchar(11) COLLATE utf8_bin NOT NULL,
  `production_product_qty` int(11) NOT NULL,
  `production_product_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_production_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_production_product`
--

TRUNCATE TABLE `app_production_product`;
-- --------------------------------------------------------

--
-- Table structure for table `app_production_production`
--

CREATE TABLE IF NOT EXISTS `app_production_production` (
  `id_production_production` int(11) NOT NULL AUTO_INCREMENT,
  `id_production` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `production_id` int(11) NOT NULL,
  `code` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id_production_production`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_production_production`
--

TRUNCATE TABLE `app_production_production`;
-- --------------------------------------------------------

--
-- Table structure for table `app_product_category`
--

CREATE TABLE IF NOT EXISTS `app_product_category` (
  `id_product_category` int(11) NOT NULL AUTO_INCREMENT,
  `product_category` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_product_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `app_product_category`
--

TRUNCATE TABLE `app_product_category`;
-- --------------------------------------------------------

--
-- Table structure for table `app_purchase`
--

CREATE TABLE IF NOT EXISTS `app_purchase` (
  `id_purchase` int(11) NOT NULL AUTO_INCREMENT,
  `id_division` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `id_auth_user` int(11) NOT NULL,
  `shipping_date` date DEFAULT NULL,
  `driver` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `licence_plate` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `purchase_note` text COLLATE utf8_bin,
  `payment_status` tinyint(1) NOT NULL COMMENT '0=transaksi baru; 1=sudah dibayar tapi belum lunas; 2=lunas',
  `total_price` decimal(10,0) NOT NULL,
  `total_price_retur` decimal(10,0) DEFAULT NULL,
  `total_hpp` decimal(10,0) NOT NULL,
  `total_discount` decimal(10,0) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_purchase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Truncate table before insert `app_purchase`
--

TRUNCATE TABLE `app_purchase`;
--
-- Dumping data for table `app_purchase`
--

INSERT INTO `app_purchase` (`id_purchase`, `id_division`, `purchase_invoice`, `id_auth_user`, `shipping_date`, `driver`, `licence_plate`, `purchase_note`, `payment_status`, `total_price`, `total_price_retur`, `total_hpp`, `total_discount`, `is_delete`, `create_date`) VALUES
(1, 3, 'INV/PCS/DIV1/06092014', 1, '2014-09-09', 'supir divisi', 'F 1 HSSA', '', 1, '3000000', '1000000', '2325000', '0', 0, '2014-09-09 06:10:20'),
(3, 3, 'INV-PRD/1/DIV1/11092014', 1, '2014-09-11', '', '', '', 1, '2400000', '700000', '2025000', '0', 0, '2014-09-11 18:50:51'),
(4, 2, 'INV-PRD/2/DIV2/11092014', 1, '2014-09-11', '', '', '', 1, '1160000', '700000', '1130000', '0', 0, '2014-09-11 18:52:36'),
(5, 3, 'INV-PRD/1/DIV1/13092014', 1, '2014-09-13', '', '', '', 0, '2000000', NULL, '1560000', '0', 0, '2014-09-13 05:43:29'),
(6, 4, 'INV-PRD/1/CBR/13092014', 1, '2014-09-13', '', '', '', 0, '1200000', NULL, '1115000', '0', 0, '2014-09-13 05:45:19');

-- --------------------------------------------------------

--
-- Table structure for table `app_purchase_payment`
--

CREATE TABLE IF NOT EXISTS `app_purchase_payment` (
  `id_purchase_payment` int(11) NOT NULL AUTO_INCREMENT,
  `id_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `payment_type` int(11) DEFAULT NULL COMMENT '1=cash;2=giro',
  `id_giro` int(11) DEFAULT NULL,
  `payment_note` text COLLATE utf8_bin,
  `payment_date` date DEFAULT NULL,
  `payment_total` decimal(10,0) DEFAULT NULL,
  `payment_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_purchase_payment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_purchase_payment`
--

TRUNCATE TABLE `app_purchase_payment`;
--
-- Dumping data for table `app_purchase_payment`
--

INSERT INTO `app_purchase_payment` (`id_purchase_payment`, `id_purchase`, `id_division`, `purchase_invoice`, `payment_type`, `id_giro`, `payment_note`, `payment_date`, `payment_total`, `payment_image`, `create_date`) VALUES
(1, 1, 3, 'INV/PCS/DIV1/06092014', 1, NULL, 'Pengurangan dari retur barang.', '2014-09-11', '1000000', NULL, '2014-09-11 13:05:50'),
(2, 4, 2, 'INV-PRD/2/DIV2/11092014', 1, NULL, 'Pengurangan dari retur barang.', '2014-09-11', '700000', NULL, '2014-09-11 18:54:50'),
(3, 3, 3, 'INV-PRD/1/DIV1/11092014', 1, NULL, 'Pengurangan dari retur barang.', '2014-09-11', '700000', NULL, '2014-09-11 18:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `app_purchase_production`
--

CREATE TABLE IF NOT EXISTS `app_purchase_production` (
  `id_purchase_production` int(11) NOT NULL AUTO_INCREMENT,
  `id_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `production_name` varchar(200) COLLATE utf8_bin NOT NULL,
  `purchase_qty` int(11) NOT NULL,
  `purchase_hpp_price` decimal(10,0) NOT NULL,
  `purchase_sales_price` decimal(10,0) NOT NULL,
  `purchase_discount_price` decimal(10,0) DEFAULT NULL,
  `production_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_purchase_production`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Truncate table before insert `app_purchase_production`
--

TRUNCATE TABLE `app_purchase_production`;
--
-- Dumping data for table `app_purchase_production`
--

INSERT INTO `app_purchase_production` (`id_purchase_production`, `id_purchase`, `id_division`, `id_item`, `purchase_invoice`, `production_code`, `production_name`, `purchase_qty`, `purchase_hpp_price`, `purchase_sales_price`, `purchase_discount_price`, `production_status`) VALUES
(1, 1, 3, 1, 'INV/PCS/DIV1/06092014', '10/412', '', 1, '765000', '1000000', '0', 0),
(2, 1, 3, 2, 'INV/PCS/DIV1/06092014', '1/211', '', 1, '1560000', '2000000', '0', 0),
(4, 3, 3, 4, 'INV-PRD/1/DIV1/11092014', '5/320', '', 1, '630000', '700000', '0', 0),
(5, 3, 3, 4, 'INV-PRD/1/DIV1/11092014', '5/321', '', 1, '630000', '700000', '0', 0),
(6, 3, 3, 1, 'INV-PRD/1/DIV1/11092014', '10/411', '', 1, '765000', '1000000', '0', 0),
(7, 4, 2, 3, 'INV-PRD/2/DIV2/11092014', '3/232', '', 1, '565000', '580000', '0', 0),
(8, 4, 2, 3, 'INV-PRD/2/DIV2/11092014', '3/230', '', 1, '565000', '580000', '0', 0),
(9, 5, 3, 2, 'INV-PRD/1/DIV1/13092014', 'WS/1', '', 1, '1560000', '2000000', '0', 0),
(10, 6, 4, 5, 'INV-PRD/1/CBR/13092014', 'CBR/0102', '', 1, '1115000', '1200000', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_purchase_retur`
--

CREATE TABLE IF NOT EXISTS `app_purchase_retur` (
  `id_purchase_retur` int(11) NOT NULL AUTO_INCREMENT,
  `id_purchase` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_purchase_production` int(11) NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin NOT NULL,
  `id_item` int(11) NOT NULL,
  `retur_qty` int(11) NOT NULL,
  `retur_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_purchase_retur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_purchase_retur`
--

TRUNCATE TABLE `app_purchase_retur`;
--
-- Dumping data for table `app_purchase_retur`
--

INSERT INTO `app_purchase_retur` (`id_purchase_retur`, `id_purchase`, `id_division`, `id_purchase_production`, `production_code`, `id_item`, `retur_qty`, `retur_price`) VALUES
(1, 1, 3, 1, '10/412', 1, 1, '1000000'),
(2, 4, 2, 7, '5/320', 4, 1, '700000'),
(3, 3, 3, 4, '5/320', 4, 1, '700000');

-- --------------------------------------------------------

--
-- Table structure for table `app_sales`
--

CREATE TABLE IF NOT EXISTS `app_sales` (
  `id_sales` int(11) NOT NULL AUTO_INCREMENT,
  `id_store` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `sales_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `id_auth_user` int(11) NOT NULL,
  `shipping_date` date DEFAULT NULL,
  `driver` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `licence_plate` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `sales_note` text COLLATE utf8_bin,
  `payment_status` tinyint(1) NOT NULL COMMENT '0=transaksi baru; 1=sudah dibayar tapi belum lunas; 2=lunas',
  `total_price` decimal(10,0) NOT NULL,
  `total_price_discount` decimal(10,0) NOT NULL,
  `total_price_retur` decimal(10,0) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sales`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_sales`
--

TRUNCATE TABLE `app_sales`;
--
-- Dumping data for table `app_sales`
--

INSERT INTO `app_sales` (`id_sales`, `id_store`, `id_division`, `sales_invoice`, `id_auth_user`, `shipping_date`, `driver`, `licence_plate`, `sales_note`, `payment_status`, `total_price`, `total_price_discount`, `total_price_retur`, `is_delete`, `create_date`) VALUES
(1, 3, 0, 'INVSLS/3/120140909', 1, '2014-09-09', '', '', '', 2, '3000000', '0', '1000000', 0, '2014-09-09 07:49:30'),
(2, 3, 0, 'INVSLS/3/220140911', 1, '2014-09-11', '', '', '', 1, '2560000', '0', '1280000', 0, '2014-09-11 18:53:44'),
(3, 3, 0, 'INVSLS/3/320140919', 1, '2014-09-19', 'Nama Supir', 'Nomer Plat', 'Dikirim nya hati2 ya!!!', 0, '1631000', '0', '0', 0, '2014-09-19 20:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `app_sales_payment`
--

CREATE TABLE IF NOT EXISTS `app_sales_payment` (
  `id_sales_payment` int(11) NOT NULL AUTO_INCREMENT,
  `id_sales` int(11) NOT NULL,
  `id_store` int(11) NOT NULL,
  `id_giro` int(11) NOT NULL,
  `sales_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `payment_type` int(11) DEFAULT NULL COMMENT '1=cash;2=giro;3=potongan lain-lain;',
  `payment_note` text COLLATE utf8_bin,
  `payment_date` date DEFAULT NULL,
  `payment_total` decimal(10,0) DEFAULT NULL,
  `payment_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sales_payment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Truncate table before insert `app_sales_payment`
--

TRUNCATE TABLE `app_sales_payment`;
--
-- Dumping data for table `app_sales_payment`
--

INSERT INTO `app_sales_payment` (`id_sales_payment`, `id_sales`, `id_store`, `id_giro`, `sales_invoice`, `payment_type`, `payment_note`, `payment_date`, `payment_total`, `payment_image`, `create_date`) VALUES
(2, 1, 3, 7, 'INVSLS/3/120140909', 2, '', '2014-09-09', '2000000', '', '2014-09-09 10:31:07'),
(4, 1, 3, 0, 'INVSLS/3/120140909', 1, 'Pengurangan dari retur barang.', '2014-09-11', '1000000', NULL, '2014-09-11 18:31:53'),
(5, 2, 3, 0, 'INVSLS/3/220140911', 1, 'Pengurangan dari retur barang.', '2014-09-11', '1280000', NULL, '2014-09-11 18:54:22');

-- --------------------------------------------------------

--
-- Table structure for table `app_sales_production`
--

CREATE TABLE IF NOT EXISTS `app_sales_production` (
  `id_sales_production` int(11) NOT NULL AUTO_INCREMENT,
  `id_sales` int(11) NOT NULL,
  `id_store` int(11) NOT NULL,
  `id_production` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `buy_price` decimal(10,0) NOT NULL,
  `sales_price` decimal(10,0) NOT NULL,
  `discount_percentage` int(11) NOT NULL,
  PRIMARY KEY (`id_sales_production`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Truncate table before insert `app_sales_production`
--

TRUNCATE TABLE `app_sales_production`;
--
-- Dumping data for table `app_sales_production`
--

INSERT INTO `app_sales_production` (`id_sales_production`, `id_sales`, `id_store`, `id_production`, `id_division`, `id_item`, `sales_qty`, `production_code`, `buy_price`, `sales_price`, `discount_percentage`) VALUES
(1, 1, 3, 2, 3, 2, 1, '1/211', '1560000', '2000000', 0),
(2, 1, 3, 1, 3, 1, 1, '10/412', '765000', '1000000', 0),
(3, 2, 3, 5, 3, 4, 1, '5/321', '700000', '700000', 0),
(4, 2, 3, 4, 3, 4, 1, '5/320', '700000', '700000', 0),
(5, 2, 3, 8, 2, 3, 1, '3/230', '580000', '580000', 0),
(6, 2, 3, 7, 2, 3, 1, '3/232', '580000', '580000', 0),
(8, 4, 3, 10, 4, 5, 1, 'CBR/0102', '1200000', '1200000', 10),
(9, 4, 3, 3, 3, 4, 1, '3/359', '700000', '700000', 5),
(10, 3, 3, 8, 2, 3, 1, '3/230', '580000', '580000', 5),
(11, 3, 3, 10, 4, 5, 1, 'CBR/0102', '1200000', '1200000', 10);

-- --------------------------------------------------------

--
-- Table structure for table `app_sales_retur`
--

CREATE TABLE IF NOT EXISTS `app_sales_retur` (
  `id_sales_retur` int(11) NOT NULL AUTO_INCREMENT,
  `id_sales_production` int(11) NOT NULL,
  `id_sales` int(11) NOT NULL,
  `id_store` int(11) NOT NULL,
  `id_production` int(11) NOT NULL,
  `id_division` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `retur_qty` int(11) NOT NULL,
  `production_code` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `retur_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_sales_retur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_sales_retur`
--

TRUNCATE TABLE `app_sales_retur`;
--
-- Dumping data for table `app_sales_retur`
--

INSERT INTO `app_sales_retur` (`id_sales_retur`, `id_sales_production`, `id_sales`, `id_store`, `id_production`, `id_division`, `id_item`, `retur_qty`, `production_code`, `retur_price`) VALUES
(2, 0, 1, 3, 2, 3, 1, 1, '10/412', '1000000'),
(3, 0, 2, 3, 4, 3, 4, 1, '5/320', '700000'),
(4, 0, 2, 3, 6, 2, 3, 1, '3/232', '580000');

-- --------------------------------------------------------

--
-- Table structure for table `app_sessions`
--

CREATE TABLE IF NOT EXISTS `app_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Truncate table before insert `app_sessions`
--

TRUNCATE TABLE `app_sessions`;
--
-- Dumping data for table `app_sessions`
--

INSERT INTO `app_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('753f78d6e25ef691e427e97e13179f65', '::1', 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36', 1411186727, 'a:3:{s:9:"user_data";s:0:"";s:18:"tmp_login_redirect";s:28:"http://localhost/app_nadera/";s:8:"ADM_SESS";a:9:{s:10:"admin_name";s:10:"Ivan Lubis";s:19:"admin_id_auth_group";s:1:"1";s:17:"admin_id_division";s:1:"1";s:18:"admin_id_auth_user";s:33:"_82926765510f1b0755895781668c1bbe";s:11:"admin_email";s:23:"ivan@deptechdigital.com";s:10:"admin_type";s:10:"superadmin";s:9:"admin_url";s:28:"http://localhost/app_nadera/";s:8:"admin_ip";s:3:"::1";s:16:"admin_last_login";s:19:"2014-01-02 17:58:55";}}'),
('e2e29f9171b95de2fa134d245eaceb1c', '::1', 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36', 1411165629, 'a:3:{s:9:"user_data";s:0:"";s:18:"tmp_login_redirect";s:28:"http://localhost/app_nadera/";s:8:"ADM_SESS";a:9:{s:10:"admin_name";s:10:"Ivan Lubis";s:19:"admin_id_auth_group";s:1:"1";s:17:"admin_id_division";s:1:"1";s:18:"admin_id_auth_user";s:33:"_82926765510f1b0755895781668c1bbe";s:11:"admin_email";s:23:"ivan@deptechdigital.com";s:10:"admin_type";s:10:"superadmin";s:9:"admin_url";s:28:"http://localhost/app_nadera/";s:8:"admin_ip";s:3:"::1";s:16:"admin_last_login";s:19:"2014-01-02 17:58:55";}}');

-- --------------------------------------------------------

--
-- Table structure for table `app_setting`
--

CREATE TABLE IF NOT EXISTS `app_setting` (
  `id_setting` int(11) NOT NULL AUTO_INCREMENT,
  `id_site` int(11) NOT NULL DEFAULT '0',
  `type` varchar(150) COLLATE utf8_bin NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_setting`),
  KEY `is_site` (`id_site`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=350 ;

--
-- Truncate table before insert `app_setting`
--

TRUNCATE TABLE `app_setting`;
--
-- Dumping data for table `app_setting`
--

INSERT INTO `app_setting` (`id_setting`, `id_site`, `type`, `value`) VALUES
(333, 1, 'app_footer', 'this is footer'),
(334, 1, 'app_title', 'NADERA STOCK APPLICATION'),
(335, 1, 'email_contact', 'ivan.z.lubis@gmail.com'),
(336, 1, 'email_contact_name', 'Ivan Lubis'),
(337, 1, 'ip_approved', '::1;127.0.0.1'),
(338, 1, 'mail_host', 'smtp.redinasti.com'),
(339, 1, 'mail_pass', 'admin12345'),
(340, 1, 'mail_port', '587'),
(341, 1, 'mail_protocol', 'smtp'),
(342, 1, 'mail_user', 'no-reply@redinasti.com'),
(343, 1, 'maintenance_message', '<p>This site currently on maintenance, please check again later.</p>\r\n'),
(344, 1, 'maintenance_mode', '0'),
(345, 1, 'multilanguage_mode', '0'),
(346, 1, 'percentage_margin', '5'),
(347, 1, 'web_description', 'This is website description'),
(348, 1, 'web_keywords', ''),
(349, 1, 'welcome_message', '');

-- --------------------------------------------------------

--
-- Table structure for table `app_sites`
--

CREATE TABLE IF NOT EXISTS `app_sites` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `site_url` varchar(255) COLLATE utf8_bin NOT NULL,
  `site_path` varchar(255) COLLATE utf8_bin NOT NULL,
  `site_logo` varchar(255) COLLATE utf8_bin NOT NULL,
  `id_ref_publish` tinyint(4) NOT NULL,
  `site_address` text COLLATE utf8_bin NOT NULL,
  `site_longitude` varchar(255) COLLATE utf8_bin NOT NULL,
  `site_latitude` varchar(255) COLLATE utf8_bin NOT NULL,
  `site_urut` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `is_delete` tinyint(4) NOT NULL,
  `modify_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id_site`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `app_sites`
--

TRUNCATE TABLE `app_sites`;
--
-- Dumping data for table `app_sites`
--

INSERT INTO `app_sites` (`id_site`, `site_name`, `site_url`, `site_path`, `site_logo`, `id_ref_publish`, `site_address`, `site_longitude`, `site_latitude`, `site_urut`, `is_default`, `is_delete`, `modify_date`, `create_date`) VALUES
(1, 'NADERA STOCK APPLICATION', '/', '/', '', 1, 'NADERA STOCK APPLICATION', '', '', 1, 1, 0, '2014-08-31 07:10:59', '2012-07-11 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `app_status`
--

CREATE TABLE IF NOT EXISTS `app_status` (
  `id_status` int(11) NOT NULL AUTO_INCREMENT,
  `status_text` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Truncate table before insert `app_status`
--

TRUNCATE TABLE `app_status`;
--
-- Dumping data for table `app_status`
--

INSERT INTO `app_status` (`id_status`, `status_text`) VALUES
(1, 'Publish'),
(2, 'Draft');

-- --------------------------------------------------------

--
-- Table structure for table `app_store`
--

CREATE TABLE IF NOT EXISTS `app_store` (
  `id_store` int(11) NOT NULL AUTO_INCREMENT,
  `store` varchar(200) COLLATE utf8_bin NOT NULL,
  `store_pic` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `store_address` text COLLATE utf8_bin,
  `store_status` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_store`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_store`
--

TRUNCATE TABLE `app_store`;
--
-- Dumping data for table `app_store`
--

INSERT INTO `app_store` (`id_store`, `store`, `store_pic`, `store_address`, `store_status`, `is_delete`, `create_date`) VALUES
(3, 'AGUNG JAYA', 'H. SOBIRIN', 'CIREBON', 0, 0, '2014-08-15 00:44:48');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier`
--

CREATE TABLE IF NOT EXISTS `app_supplier` (
  `id_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `supplier` varchar(200) COLLATE utf8_bin NOT NULL,
  `supplier_pic` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `supplier_address` text COLLATE utf8_bin,
  `supplier_status` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_supplier`
--

TRUNCATE TABLE `app_supplier`;
--
-- Dumping data for table `app_supplier`
--

INSERT INTO `app_supplier` (`id_supplier`, `supplier`, `supplier_pic`, `supplier_address`, `supplier_status`, `is_delete`, `create_date`) VALUES
(1, 'Supplier 2', 'Bpk. Anukaa', 'jalan anukan raya no 10a', 0, 0, '2014-06-05 17:22:25'),
(2, 'Supplier 1', 'Bpk. Anukannya', 'ini alamat saya ya', 0, 0, '2014-06-30 06:37:31'),
(3, 'Guna Kinia', 'Edi 087877887893', 'JALAN GAJAH MADA, jakarta', 0, 0, '2014-08-23 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_credit`
--

CREATE TABLE IF NOT EXISTS `app_supplier_credit` (
  `id_supplier_credit` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier_purchase` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `credit_price` decimal(10,0) NOT NULL,
  `credit_note` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `credit_status` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier_credit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Truncate table before insert `app_supplier_credit`
--

TRUNCATE TABLE `app_supplier_credit`;
--
-- Dumping data for table `app_supplier_credit`
--

INSERT INTO `app_supplier_credit` (`id_supplier_credit`, `id_supplier_purchase`, `id_supplier`, `purchase_invoice`, `credit_price`, `credit_note`, `credit_status`, `create_date`) VALUES
(1, 1, 3, 'INV/PCS/109014', '1890000', '', 1, '2014-09-01 09:02:44'),
(6, 2, 3, 'INV/PCS/309014', '463050', 'Pengurangan dari retur barang.', 1, '2014-09-10 17:27:30'),
(7, 2, 3, 'INV/PCS/309014', '86625', 'Pengurangan dari retur barang.', 1, '2014-09-10 17:27:30'),
(8, 3, 3, 'INV/PCS/GUNAKINIA/06092014', '115500', 'Pengurangan dari retur barang.', 1, '2014-09-13 07:24:04');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_product`
--

CREATE TABLE IF NOT EXISTS `app_supplier_product` (
  `id_supplier_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  PRIMARY KEY (`id_supplier_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_supplier_product`
--

TRUNCATE TABLE `app_supplier_product`;
--
-- Dumping data for table `app_supplier_product`
--

INSERT INTO `app_supplier_product` (`id_supplier_product`, `id_supplier`, `id_product`) VALUES
(1, 3, 3),
(2, 3, 4),
(3, 3, 6),
(4, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_purchase`
--

CREATE TABLE IF NOT EXISTS `app_supplier_purchase` (
  `id_supplier_purchase` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `id_auth_user` int(11) NOT NULL,
  `shipping_date` date DEFAULT NULL,
  `driver` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `licence_plate` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `purchase_note` text COLLATE utf8_bin,
  `payment_status` tinyint(1) NOT NULL COMMENT '0=transaksi baru; 1=sudah dibayar tapi belum lunas; 2=lunas',
  `total_price` decimal(10,0) NOT NULL,
  `total_price_retur` decimal(10,0) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier_purchase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_supplier_purchase`
--

TRUNCATE TABLE `app_supplier_purchase`;
--
-- Dumping data for table `app_supplier_purchase`
--

INSERT INTO `app_supplier_purchase` (`id_supplier_purchase`, `id_supplier`, `purchase_invoice`, `id_auth_user`, `shipping_date`, `driver`, `licence_plate`, `purchase_note`, `payment_status`, `total_price`, `total_price_retur`, `is_delete`, `create_date`) VALUES
(1, 3, 'INV/PCS/109014', 0, '2014-08-31', 'supir guna kinia', 'F 1 HSSA', '', 2, '8110000', NULL, 0, '2014-08-31 18:08:41'),
(2, 3, 'INV/PCS/309014', 0, '2014-09-03', 'supir guna kinia', 'F 1 HSSA', '', 1, '5990300', '549675', 0, '2014-09-03 05:38:37'),
(3, 3, 'INV/PCS/GUNAKINIA/06092014', 0, '2014-09-06', 'supir  nya', 'F 1 HSSA', '', 0, '15732500', '115500', 0, '2014-09-06 03:45:59'),
(4, 3, 'INV13092014', 0, '2014-09-13', '', '', '', 1, '3200000', NULL, 0, '2014-09-13 07:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_purchase_payment`
--

CREATE TABLE IF NOT EXISTS `app_supplier_purchase_payment` (
  `id_supplier_purchase_payment` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier_purchase` int(11) NOT NULL,
  `purchase_invoice` varchar(100) COLLATE utf8_bin NOT NULL,
  `payment_type` int(11) DEFAULT NULL COMMENT '1=cash;2=giro',
  `id_giro` int(11) DEFAULT NULL,
  `payment_note` text COLLATE utf8_bin,
  `payment_date` date DEFAULT NULL,
  `payment_total` decimal(10,0) DEFAULT NULL,
  `payment_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier_purchase_payment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Truncate table before insert `app_supplier_purchase_payment`
--

TRUNCATE TABLE `app_supplier_purchase_payment`;
--
-- Dumping data for table `app_supplier_purchase_payment`
--

INSERT INTO `app_supplier_purchase_payment` (`id_supplier_purchase_payment`, `id_supplier_purchase`, `purchase_invoice`, `payment_type`, `id_giro`, `payment_note`, `payment_date`, `payment_total`, `payment_image`, `create_date`) VALUES
(1, 1, 'INV/PCS/109014', 2, 2, NULL, '2014-09-01', '4000000', NULL, '2014-09-01 09:02:44'),
(2, 1, 'INV/PCS/109014', 2, 3, NULL, '2014-09-01', '6000000', NULL, '2014-09-01 09:02:44'),
(3, 2, 'INV/PCS/309014', 1, NULL, 'Diambil dari piutang supplier.', '2014-09-03', '1890000', NULL, '2014-09-03 05:38:37'),
(4, 4, 'INV13092014', 1, NULL, 'Diambil dari piutang supplier.', '2014-09-13', '665175', NULL, '2014-09-13 07:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_purchase_product`
--

CREATE TABLE IF NOT EXISTS `app_supplier_purchase_product` (
  `id_supplier_purchase_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier_purchase` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `purchase_qty` int(11) NOT NULL,
  `purchase_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_supplier_purchase_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Truncate table before insert `app_supplier_purchase_product`
--

TRUNCATE TABLE `app_supplier_purchase_product`;
--
-- Dumping data for table `app_supplier_purchase_product`
--

INSERT INTO `app_supplier_purchase_product` (`id_supplier_purchase_product`, `id_supplier_purchase`, `id_supplier`, `id_product`, `purchase_qty`, `purchase_price`) VALUES
(1, 1, 3, 12, 20, '290000'),
(2, 1, 3, 2, 15, '147000'),
(3, 1, 3, 6, 5, '21000'),
(4, 2, 3, 2, 10, '154350'),
(5, 2, 3, 5, 50, '26250'),
(6, 2, 3, 8, 4, '278250'),
(7, 2, 3, 10, 100, '14438'),
(8, 2, 3, 4, 100, '5775'),
(9, 3, 3, 13, 400, '35000'),
(10, 3, 3, 7, 75, '23100'),
(11, 4, 3, 15, 400, '8000');

-- --------------------------------------------------------

--
-- Table structure for table `app_supplier_purchase_retur`
--

CREATE TABLE IF NOT EXISTS `app_supplier_purchase_retur` (
  `id_supplier_purchase_retur` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier_purchase` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `retur_qty` int(11) NOT NULL,
  `retur_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_supplier_purchase_retur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `app_supplier_purchase_retur`
--

TRUNCATE TABLE `app_supplier_purchase_retur`;
--
-- Dumping data for table `app_supplier_purchase_retur`
--

INSERT INTO `app_supplier_purchase_retur` (`id_supplier_purchase_retur`, `id_supplier_purchase`, `id_supplier`, `id_product`, `retur_qty`, `retur_price`) VALUES
(1, 2, 3, 2, 3, '154350'),
(2, 2, 3, 4, 15, '5775'),
(3, 3, 3, 7, 5, '23100');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
