-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.16-log - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных groups_sch
CREATE DATABASE IF NOT EXISTS `groups_sch` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `groups_sch`;

-- Дамп структуры для таблица groups_sch.calendar
DROP TABLE IF EXISTS `calendar`;
CREATE TABLE IF NOT EXISTS `calendar` (
  `group_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `weekday` int(3) NOT NULL,
  `lesson` int(4) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `type` varchar(32) NOT NULL,
  `time_start` varchar(5) NOT NULL,
  `time_end` varchar(5) NOT NULL,
  `teachers` varchar(256) NOT NULL,
  `places` varchar(256) NOT NULL,
  PRIMARY KEY (`group_id`,`day`,`lesson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.calendar_dynamic
DROP TABLE IF EXISTS `calendar_dynamic`;
CREATE TABLE IF NOT EXISTS `calendar_dynamic` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `weekday` int(3) NOT NULL,
  `lesson` int(4) NOT NULL,
  `is_odd` int(1) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `time_start` varchar(5) DEFAULT NULL,
  `time_end` varchar(5) DEFAULT NULL,
  `teachers` varchar(1024) DEFAULT NULL,
  `places` varchar(1024) DEFAULT NULL,
  `chain` int(11) NOT NULL,
  `action` enum('CHANGE','ERASE') NOT NULL,
  PRIMARY KEY (`group_id`,`day`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.calendar_static
DROP TABLE IF EXISTS `calendar_static`;
CREATE TABLE IF NOT EXISTS `calendar_static` (
  `id` int(32) NOT NULL,
  `group_id` int(11) NOT NULL,
  `weekday` int(3) NOT NULL,
  `lesson` int(4) NOT NULL,
  `is_odd` int(1) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `type` varchar(32) NOT NULL,
  `time_start` varchar(5) NOT NULL,
  `time_end` varchar(5) NOT NULL,
  `teachers` varchar(1024) NOT NULL,
  `places` varchar(1024) NOT NULL,
  PRIMARY KEY (`group_id`,`lesson`,`weekday`,`is_odd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.calendar_stored
DROP TABLE IF EXISTS `calendar_stored`;
CREATE TABLE IF NOT EXISTS `calendar_stored` (
  `group_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `weekday` int(3) NOT NULL,
  `lesson` int(4) NOT NULL,
  `is_odd` int(1) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `time_start` varchar(5) DEFAULT NULL,
  `time_end` varchar(5) DEFAULT NULL,
  `teachers` varchar(1024) DEFAULT NULL,
  `places` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`group_id`,`day`,`weekday`,`lesson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.calendar_user
DROP TABLE IF EXISTS `calendar_user`;
CREATE TABLE IF NOT EXISTS `calendar_user` (
  `group_id` int(11) NOT NULL,
  `day` date DEFAULT NULL,
  `weekday` int(3) NOT NULL,
  `lesson` int(4) NOT NULL,
  `is_odd` int(1) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `time_start` varchar(5) DEFAULT NULL,
  `time_end` varchar(5) DEFAULT NULL,
  `teachers` varchar(1024) DEFAULT NULL,
  `places` varchar(1024) DEFAULT NULL,
  `action` enum('CHANGE','ERASE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.confirm_hash
DROP TABLE IF EXISTS `confirm_hash`;
CREATE TABLE IF NOT EXISTS `confirm_hash` (
  `login` varchar(32) DEFAULT NULL,
  `for` enum('ACCOUNT','PASSWORD','EMAIL') DEFAULT NULL,
  `value` varchar(32) DEFAULT NULL,
  `stored` varchar(64) DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `lifetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.contributors
DROP TABLE IF EXISTS `contributors`;
CREATE TABLE IF NOT EXISTS `contributors` (
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.groups
DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `name` varchar(16) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `header_login` varchar(32) DEFAULT NULL,
  `cache` int(1) DEFAULT '0',
  `cache_last` datetime DEFAULT NULL,
  `static_changed` datetime DEFAULT NULL,
  `cache_static` int(1) DEFAULT '0',
  `university_id` smallint(6) DEFAULT NULL,
  `recache_count` int(11) DEFAULT '5',
  `year` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `faculty_name` varchar(128) DEFAULT NULL,
  `faculty_abbr` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.head_request
DROP TABLE IF EXISTS `head_request`;
CREATE TABLE IF NOT EXISTS `head_request` (
  `user_id` int(11) DEFAULT NULL,
  `number` varchar(17) DEFAULT NULL,
  `vk_link` varchar(64) DEFAULT NULL,
  `requested` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.homeworks
DROP TABLE IF EXISTS `homeworks`;
CREATE TABLE IF NOT EXISTS `homeworks` (
  `group_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `lesson` int(11) NOT NULL,
  `text` varchar(1000) DEFAULT NULL,
  `added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`,`date`,`lesson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для процедура groups_sch.homework_delete
DROP PROCEDURE IF EXISTS `homework_delete`;
DELIMITER //
CREATE DEFINER=`root`@`%` PROCEDURE `homework_delete`()
BEGIN
DECLARE curr_time DATETIME DEFAULT NOW();
DELETE FROM users WHERE users.created < DATE_ADD(curr_time, INTERVAL 6 HOUR) AND users.active = 0;
DELETE FROM confirm_hash WHERE confirm_hash.lifetime < curr_time;
END//
DELIMITER ;

-- Дамп структуры для таблица groups_sch.move_request
DROP TABLE IF EXISTS `move_request`;
CREATE TABLE IF NOT EXISTS `move_request` (
  `head_id` int(11) DEFAULT NULL,
  `move_from` int(11) DEFAULT NULL,
  `move_to` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `group_id` int(11) NOT NULL,
  `refers_to` enum('ALL','GROUP','DAY') NOT NULL,
  `type` enum('NOTIFICATION','ALERT','WARNING') NOT NULL,
  `visibility` enum('ALL','SELF') NOT NULL,
  `date` date NOT NULL COMMENT 'Used if refers_to = day or lesson',
  `text` varchar(300) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `added` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для событие groups_sch.request_clear
DROP EVENT IF EXISTS `request_clear`;
DELIMITER //
CREATE DEFINER=`root`@`%` EVENT `request_clear` ON SCHEDULE EVERY 1 HOUR STARTS '2017-11-26 00:00:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
DECLARE curr_time DATETIME DEFAULT NOW();
DELETE FROM users WHERE users.created < DATE_ADD(curr_time, INTERVAL 6 HOUR) AND users.active = 0;
DELETE FROM confirm_hash WHERE confirm_hash.lifetime < curr_time;
END//
DELIMITER ;

-- Дамп структуры для таблица groups_sch.uploads
DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `name` varchar(64) NOT NULL,
  `original_name` varchar(64) NOT NULL,
  `showable` int(1) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `hash` varchar(32) NOT NULL,
  `adder_id` int(11) NOT NULL,
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stored_untill` date NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица groups_sch.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password_hash` varchar(60) DEFAULT NULL,
  `password_changed` datetime DEFAULT CURRENT_TIMESTAMP,
  `session_hash` varchar(64) DEFAULT NULL,
  `last_ip` varchar(15) DEFAULT NULL,
  `number` varchar(17) DEFAULT NULL,
  `vk_id` varchar(64) DEFAULT NULL,
  `is_head` int(1) NOT NULL DEFAULT '0',
  `group` varchar(16) NOT NULL DEFAULT '0',
  `rights_group` int(11) DEFAULT '1',
  `privileges` int(11) NOT NULL DEFAULT '16',
  `verified` int(1) DEFAULT '0',
  `active` int(1) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `year` int(11) NOT NULL,
  KEY `key` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
