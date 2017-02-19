# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.33)
# Database: nerdery_sample_beardsley
# Generation Time: 2017-02-18 23:49:12 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ci_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table snack_purchase_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `snack_purchase_type`;

CREATE TABLE `snack_purchase_type` (
  `snack_purchase_type_id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `snack_purchase_type` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`snack_purchase_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `snack_purchase_type` WRITE;
/*!40000 ALTER TABLE `snack_purchase_type` DISABLE KEYS */;

INSERT INTO `snack_purchase_type` (`snack_purchase_type_id`, `snack_purchase_type`)
VALUES
	(1,'Always'),
	(2,'Electable'),
	(3,'Potential'),
	(4,'Disabled');

/*!40000 ALTER TABLE `snack_purchase_type` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table snacks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `snacks`;

CREATE TABLE `snacks` (
  `snack_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `snack_name` varchar(250) NOT NULL DEFAULT '',
  `snack_purchase_type_id` int(1) unsigned NOT NULL DEFAULT '2',
  `snack_location_short` varchar(50) NOT NULL DEFAULT '',
  `snack_location_long` text,
  `longitude` decimal(10,6) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `last_date_purchased` date NOT NULL DEFAULT '0000-00-00',
  `date_deleted` date NOT NULL DEFAULT '0000-00-00',
  `webservice_snack_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`snack_id`),
  KEY `snack_status` (`snack_purchase_type_id`),
  CONSTRAINT `snacks_ibfk_1` FOREIGN KEY (`snack_purchase_type_id`) REFERENCES `snack_purchase_type` (`snack_purchase_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) NOT NULL DEFAULT '',
  `user_session_id` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users_snacks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_snacks`;

CREATE TABLE `users_snacks` (
  `snack_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`snack_id`,`user_id`,`date`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_snacks_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`snack_id`),
  CONSTRAINT `users_snacks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users_snacks_suggestions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_snacks_suggestions`;

CREATE TABLE `users_snacks_suggestions` (
  `snack_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`snack_id`,`user_id`,`date`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_snacks_suggestions_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`snack_id`),
  CONSTRAINT `users_snacks_suggestions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
