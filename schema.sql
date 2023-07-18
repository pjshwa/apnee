-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- 생성 시간: 23-07-18 20:23
-- 서버 버전: 8.0.15
-- PHP 버전: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `pjshwa`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `cats`
--

CREATE TABLE `cats` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `cowgame_score`
--

CREATE TABLE `cowgame_score` (
  `id` int(6) UNSIGNED NOT NULL,
  `score` int(6) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `daily_problems`
--

CREATE TABLE `daily_problems` (
  `ID` int(11) NOT NULL,
  `url` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `eggs`
--

CREATE TABLE `eggs` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 트리거 `eggs`
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
-- 테이블 구조 `egg_comments`
--

CREATE TABLE `egg_comments` (
  `id` int(6) UNSIGNED NOT NULL,
  `egg_id` int(6) UNSIGNED NOT NULL,
  `comment_id` int(6) DEFAULT NULL,
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `author` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `events`
--

CREATE TABLE `events` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `important`
--

CREATE TABLE `important` (
  `id` int(6) UNSIGNED NOT NULL,
  `canting` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `have_been` int(1) NOT NULL DEFAULT '0',
  `meal` int(1) NOT NULL DEFAULT '0',
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `review` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `phi_chats`
--

CREATE TABLE `phi_chats` (
  `id` int(6) UNSIGNED NOT NULL,
  `phi_id` int(6) NOT NULL,
  `content` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `src` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `phi_info`
--

CREATE TABLE `phi_info` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_src` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `pika_score`
--

CREATE TABLE `pika_score` (
  `id` int(6) UNSIGNED NOT NULL,
  `nickname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `success` int(1) NOT NULL,
  `remain_time` int(6) NOT NULL,
  `hits_score` int(10) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `queries`
--

CREATE TABLE `queries` (
  `id` int(6) UNSIGNED NOT NULL,
  `a_query` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `questions`
--

CREATE TABLE `questions` (
  `question_key` int(11) NOT NULL,
  `question` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tries` int(11) NOT NULL DEFAULT '0',
  `corrects` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `temp_questions`
--

CREATE TABLE `temp_questions` (
  `question_key` int(11) NOT NULL,
  `question` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `uncle_pic`
--

CREATE TABLE `uncle_pic` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `visitor_log`
--

CREATE TABLE `visitor_log` (
  `ID` int(11) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `cats`
--
ALTER TABLE `cats`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `cowgame_score`
--
ALTER TABLE `cowgame_score`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `daily_problems`
--
ALTER TABLE `daily_problems`
  ADD PRIMARY KEY (`ID`);

--
-- 테이블의 인덱스 `eggs`
--
ALTER TABLE `eggs`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `egg_comments`
--
ALTER TABLE `egg_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `egg_id` (`egg_id`);

--
-- 테이블의 인덱스 `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `important`
--
ALTER TABLE `important`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `phi_chats`
--
ALTER TABLE `phi_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phi_id` (`phi_id`);

--
-- 테이블의 인덱스 `phi_info`
--
ALTER TABLE `phi_info`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `pika_score`
--
ALTER TABLE `pika_score`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_key`);

--
-- 테이블의 인덱스 `temp_questions`
--
ALTER TABLE `temp_questions`
  ADD PRIMARY KEY (`question_key`);

--
-- 테이블의 인덱스 `uncle_pic`
--
ALTER TABLE `uncle_pic`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD PRIMARY KEY (`ID`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `cats`
--
ALTER TABLE `cats`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `cowgame_score`
--
ALTER TABLE `cowgame_score`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `daily_problems`
--
ALTER TABLE `daily_problems`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `eggs`
--
ALTER TABLE `eggs`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `egg_comments`
--
ALTER TABLE `egg_comments`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `events`
--
ALTER TABLE `events`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `important`
--
ALTER TABLE `important`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `phi_chats`
--
ALTER TABLE `phi_chats`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `phi_info`
--
ALTER TABLE `phi_info`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `pika_score`
--
ALTER TABLE `pika_score`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `queries`
--
ALTER TABLE `queries`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `questions`
--
ALTER TABLE `questions`
  MODIFY `question_key` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `temp_questions`
--
ALTER TABLE `temp_questions`
  MODIFY `question_key` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `uncle_pic`
--
ALTER TABLE `uncle_pic`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `visitor_log`
--
ALTER TABLE `visitor_log`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
