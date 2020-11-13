-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 14, 2020 at 02:40 AM
-- Server version: 8.0.15
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pjshwa`
--

-- --------------------------------------------------------

--
-- Table structure for table `cats`
--

CREATE TABLE `cats` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) NOT NULL,
  `img_src` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(6) UNSIGNED NOT NULL,
  `author` varchar(30) NOT NULL,
  `content` varchar(30) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `coffee`
--

CREATE TABLE `coffee` (
  `ID` int(11) NOT NULL,
  `iced_americano` int(11) NOT NULL,
  `reg_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `cowgame_score`
--

CREATE TABLE `cowgame_score` (
  `id` int(6) UNSIGNED NOT NULL,
  `score` int(6) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `eggs`
--

CREATE TABLE `eggs` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

--
-- Triggers `eggs`
--
DELIMITER $$
CREATE TRIGGER `eggs_before_insert` BEFORE INSERT ON `eggs` FOR EACH ROW BEGIN
IF NEW.title IS NULL OR NEW.title = '' THEN
	SET NEW.title = DATE_FORMAT(CURRENT_DATE, '%Y%m%d');
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `egg_comments`
--

CREATE TABLE `egg_comments` (
  `id` int(6) UNSIGNED NOT NULL,
  `egg_id` int(6) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `author` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `fish`
--

CREATE TABLE `fish` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) NOT NULL,
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `fishbowl_users`
--

CREATE TABLE `fishbowl_users` (
  `user_id` int(6) UNSIGNED NOT NULL,
  `nickname` varchar(60) NOT NULL,
  `password_h` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `gongboo`
--

CREATE TABLE `gongboo` (
  `id` int(6) UNSIGNED NOT NULL,
  `category_id` int(6) NOT NULL,
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `include_highlighter` tinyint(1) NOT NULL DEFAULT '0',
  `include_markdown` tinyint(1) NOT NULL DEFAULT '0',
  `raw_link` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `gongboo_categories`
--

CREATE TABLE `gongboo_categories` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `important`
--

CREATE TABLE `important` (
  `id` int(6) UNSIGNED NOT NULL,
  `canting` varchar(60) NOT NULL,
  `img_src` varchar(255) NOT NULL,
  `have_been` int(1) NOT NULL DEFAULT '0',
  `meal` int(1) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `review` text NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `phi_chats`
--

CREATE TABLE `phi_chats` (
  `id` int(6) UNSIGNED NOT NULL,
  `phi_id` int(6) NOT NULL,
  `content` varchar(100) CHARACTER SET euckr COLLATE euckr_korean_ci NOT NULL,
  `src` varchar(64) NOT NULL DEFAULT '#',
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `phi_info`
--

CREATE TABLE `phi_info` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `img_src` varchar(60) NOT NULL,
  `desc` varchar(60) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `pika_score`
--

CREATE TABLE `pika_score` (
  `id` int(6) UNSIGNED NOT NULL,
  `nickname` varchar(60) NOT NULL,
  `success` int(1) NOT NULL,
  `remain_time` int(6) NOT NULL,
  `hits_score` int(10) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(6) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `id` int(6) UNSIGNED NOT NULL,
  `a_query` varchar(30) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_key` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` varchar(64) NOT NULL,
  `tries` int(11) NOT NULL DEFAULT '0',
  `corrects` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `temp_questions`
--

CREATE TABLE `temp_questions` (
  `question_key` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `uncle_pic`
--

CREATE TABLE `uncle_pic` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) NOT NULL,
  `img_src` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

-- --------------------------------------------------------

--
-- Table structure for table `visitor_log`
--

CREATE TABLE `visitor_log` (
  `ID` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cats`
--
ALTER TABLE `cats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coffee`
--
ALTER TABLE `coffee`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `index_reg_date_on_coffee` (`reg_date`);

--
-- Indexes for table `cowgame_score`
--
ALTER TABLE `cowgame_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eggs`
--
ALTER TABLE `eggs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `egg_comments`
--
ALTER TABLE `egg_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `egg_id` (`egg_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fish`
--
ALTER TABLE `fish`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fishbowl_users`
--
ALTER TABLE `fishbowl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `gongboo`
--
ALTER TABLE `gongboo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gongboo_categories`
--
ALTER TABLE `gongboo_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `important`
--
ALTER TABLE `important`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phi_chats`
--
ALTER TABLE `phi_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phi_id` (`phi_id`);

--
-- Indexes for table `phi_info`
--
ALTER TABLE `phi_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pika_score`
--
ALTER TABLE `pika_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_key`);

--
-- Indexes for table `temp_questions`
--
ALTER TABLE `temp_questions`
  ADD PRIMARY KEY (`question_key`);

--
-- Indexes for table `uncle_pic`
--
ALTER TABLE `uncle_pic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cats`
--
ALTER TABLE `cats`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coffee`
--
ALTER TABLE `coffee`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cowgame_score`
--
ALTER TABLE `cowgame_score`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eggs`
--
ALTER TABLE `eggs`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `egg_comments`
--
ALTER TABLE `egg_comments`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fish`
--
ALTER TABLE `fish`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fishbowl_users`
--
ALTER TABLE `fishbowl_users`
  MODIFY `user_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gongboo`
--
ALTER TABLE `gongboo`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gongboo_categories`
--
ALTER TABLE `gongboo_categories`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `important`
--
ALTER TABLE `important`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phi_chats`
--
ALTER TABLE `phi_chats`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phi_info`
--
ALTER TABLE `phi_info`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pika_score`
--
ALTER TABLE `pika_score`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queries`
--
ALTER TABLE `queries`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_key` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_questions`
--
ALTER TABLE `temp_questions`
  MODIFY `question_key` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uncle_pic`
--
ALTER TABLE `uncle_pic`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor_log`
--
ALTER TABLE `visitor_log`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
