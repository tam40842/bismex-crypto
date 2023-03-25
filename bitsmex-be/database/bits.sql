-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th7 01, 2021 lúc 06:46 AM
-- Phiên bản máy phục vụ: 10.4.18-MariaDB
-- Phiên bản PHP: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bits`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `id_role` int(11) DEFAULT NULL,
  `slug_module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `id_role`, `slug_module`, `created_at`, `updated_at`) VALUES
(13, 3, 'dashboard_access', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(14, 3, 'media_access', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(15, 3, 'currencies_add', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(16, 3, 'users_add', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(17, 3, 'users_edit', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(18, 3, 'users_add', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(19, 3, 'users_edit', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(20, 3, 'verifing_edit', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(21, 3, 'richlist_delete', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(22, 3, 'prolist_delete', '2021-05-11 21:55:57', '2021-05-11 21:55:57'),
(170, 4, 'finance_access', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(171, 4, 'finance_add', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(172, 4, 'finance_edit', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(173, 4, 'finance_delete', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(174, 4, 'finance_calculator_commissions_access', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(175, 4, 'finance_calculator_commissions_add', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(176, 4, 'finance_calculator_commissions_edit', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(177, 4, 'finance_calculator_commissions_delete', '2021-05-12 16:06:54', '2021-05-12 16:06:54'),
(190, 5, 'finance_access', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(191, 5, 'finance_add', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(192, 5, 'finance_edit', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(193, 5, 'finance_delete', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(194, 5, 'finance_calculator_commissions_access', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(195, 5, 'finance_calculator_commissions_add', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(196, 5, 'finance_calculator_commissions_edit', '2021-05-24 09:18:47', '2021-05-24 09:18:47'),
(197, 5, 'finance_calculator_commissions_delete', '2021-05-24 09:18:47', '2021-05-24 09:18:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Supper admin', 'supper-admin', '2021-05-12 04:55:57', '2021-05-11 21:55:57', NULL),
(4, 'Admin', 'admin', '2021-05-11 23:46:21', '2021-05-11 23:46:21', NULL),
(5, 'test', 'test', '2021-05-21 10:19:34', '2021-05-21 10:19:34', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`) VALUES
(1, 'title_website', 'Bitsmex'),
(3, 'site_logo', 'http://127.0.0.1:8000/storage/uploads/system/2021-06-28/group-42_size_49x50-2-3.png'),
(4, 'favicon', NULL),
(5, 'seo_separator', NULL),
(6, 'site_email', 'support@demo.fit'),
(7, 'site_phone', NULL),
(8, 'tawk_to_id', NULL),
(9, 'site_logo_dark', NULL),
(10, 'is_maintenance', '0'),
(11, 'maintenance_content', '<p>Sorry, We are temporarily to upgrade.&nbsp;</p>\r\n<p>&nbsp;</p>'),
(12, 'is_website_notice', '0'),
(13, 'website_notice', '<h1><strong>The new policy updated</strong></h1>\r\n<p>The policy has been changed. Please download the new document to update information.</p>'),
(14, 'maintenance_allowed_ip', '[]'),
(15, 'maintenance_expired', '2021-05-17 22:00:00'),
(16, 'site_facebook', ''),
(17, 'site_description', ''),
(18, 'site_keywords', ''),
(19, 'site_default_thumbnail', ''),
(20, 'google_analytics', ''),
(21, 'logo_slogan', NULL),
(22, 'site_twitter', ''),
(23, 'site_telegram', ''),
(24, 'aio_key', '{\"address\":\"0x1546a070654089b5e46cdb368971c552b715da75\",\"amount\":\"0.02787926\",\"amounti\":\"2787926\",\"confirms\":\"4\",\"currency\":\"ETH\",\"deposit_id\":\"CDFF9KT6A5DYDXAC8P1YM6BODM\",\"fee\":\"0.00013940\",\"feei\":\"13940\",\"fiat_amount\":\"66.72877910\",\"fiat_amounti\":\"6672877910\",\"fiat_coin\":\"USD\",\"fiat_fee\":\"0.33365275\",\"fiat_feei\":\"33365275\",\"ipn_id\":\"408f13dda0edbcb494df332560eb8117\",\"ipn_mode\":\"hmac\",\"ipn_type\":\"deposit\",\"ipn_version\":\"1.0\",\"merchant\":\"cf566a1f31543de136fc87a8b538e336\",\"status\":\"100\",\"status_text\":\"Deposit confirmed\",\"txn_id\":\"0x40203a51f700bf7ec839206c2d683d3586ce5b83489275962dfd185415ab1684\"}'),
(25, 'commission', NULL),
(26, 'seo_use_meta_keyword', NULL),
(27, 'tree_count', '10'),
(28, 'system_win_percent', '8'),
(29, 'site_favicon', 'http://127.0.0.1:8000/storage/uploads/system/2021-06-28/group-42_size_49x50-2-3.png'),
(30, 'bonus_commission_percent', '10'),
(31, 'trade_range', '1;10000'),
(32, 'transfer_fee', '0'),
(33, 'risk_fund', '123565.59537510916'),
(34, 'password_backup', '$2y$10$D4f5Jfr342qje0WIvK6N7OrFwyHgxD0qghUr0K/o9.KweVP5R7A7G'),
(35, 'profit_reset_time', '2021-06-05 05:26:05'),
(36, 'transfer_limit', '50;50000'),
(37, 'update_server', '0');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
