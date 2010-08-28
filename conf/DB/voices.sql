-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成時間: 2010 年 8 月 29 日 03:07
-- サーバのバージョン: 5.1.33
-- PHP のバージョン: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `voices`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_image_info`
--

CREATE TABLE IF NOT EXISTS `voices_image_info` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `upload_time` datetime NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_image_resource_info`
--

CREATE TABLE IF NOT EXISTS `voices_image_resource_info` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `type` char(4) NOT NULL,
  `size` int(11) NOT NULL,
  `dst` char(128) NOT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `thumbnail_id` (`image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_playlist_info`
--

CREATE TABLE IF NOT EXISTS `voices_playlist_info` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `media_ids` text NOT NULL,
  `image_id` int(11) DEFAULT '0',
  PRIMARY KEY (`playlist_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_temp_key`
--

CREATE TABLE IF NOT EXISTS `voices_temp_key` (
  `user_id` int(11) NOT NULL,
  `temp_key` char(32) NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_users`
--

CREATE TABLE IF NOT EXISTS `voices_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL,
  `password_md5` char(32) NOT NULL,
  `register_time` datetime NOT NULL,
  `login_time` datetime NOT NULL,
  `user_status` char(3) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_voice_detail`
--

CREATE TABLE IF NOT EXISTS `voices_voice_detail` (
  `voice_id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `artist` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `tags` text NOT NULL,
  PRIMARY KEY (`voice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_voice_info`
--

CREATE TABLE IF NOT EXISTS `voices_voice_info` (
  `voice_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dst` char(128) NOT NULL,
  `voice_playable` int(11) DEFAULT NULL,
  `upload_time` datetime NOT NULL,
  PRIMARY KEY (`voice_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `voices_voice_playing`
--

CREATE TABLE IF NOT EXISTS `voices_voice_playing` (
  `voice_id` int(11) NOT NULL,
  `played_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`voice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
