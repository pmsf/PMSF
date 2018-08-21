/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for Monocle
CREATE DATABASE IF NOT EXISTS `Monocle` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `Monocle`;

-- Dumping structure for table Monocle.common
CREATE TABLE IF NOT EXISTS `common` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `val` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_common_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.forts
CREATE TABLE IF NOT EXISTS `forts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(35) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `sponsor` smallint(6) DEFAULT NULL,
  `weather_cell_id` bigint(20) unsigned DEFAULT NULL,
  `park` varchar(128) DEFAULT NULL,
  `parkid` bigint(20) DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id` (`external_id`),
  KEY `ix_coords` (`lat`,`lon`)
) ENGINE=InnoDB AUTO_INCREMENT=577 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.fort_sightings
CREATE TABLE IF NOT EXISTS `fort_sightings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fort_id` int(11) DEFAULT NULL,
  `last_modified` int(11) DEFAULT NULL,
  `team` tinyint(3) unsigned DEFAULT NULL,
  `guard_pokemon_id` smallint(6) DEFAULT NULL,
  `slots_available` smallint(6) DEFAULT NULL,
  `is_in_battle` tinyint(1) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fort_id_last_modified_unique` (`fort_id`,`last_modified`),
  KEY `ix_fort_sightings_last_modified` (`last_modified`),
  CONSTRAINT `fort_sightings_ibfk_1` FOREIGN KEY (`fort_id`) REFERENCES `forts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=512 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.nests
CREATE TABLE IF NOT EXISTS `nests` (
  `nest_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `pokemon_id` int(11) DEFAULT 0,
  `updated` bigint(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `nest_submitted_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`nest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `selly_id` varchar(100) NOT NULL,
  `product_id` int(30) NOT NULL,
  `email` varchar(250) NOT NULL,
  `value` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.pokestops
CREATE TABLE IF NOT EXISTS `pokestops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(35) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `quest_id` smallint(4) DEFAULT NULL,
  `reward_id` smallint(4) DEFAULT NULL,
  `deployer` varchar(40) DEFAULT NULL,
  `lure_start` varchar(40) DEFAULT NULL,
  `expires` int(11) DEFAULT NULL,
  `quest_submitted_by` varchar(200) DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id` (`external_id`),
  KEY `ix_pokestops_lon` (`lon`),
  KEY `ix_pokestops_lat` (`lat`)
) ENGINE=InnoDB AUTO_INCREMENT=2048 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.raids
CREATE TABLE IF NOT EXISTS `raids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` bigint(20) DEFAULT NULL,
  `fort_id` int(11) DEFAULT NULL,
  `level` tinyint(3) unsigned DEFAULT NULL,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `time_spawn` int(11) DEFAULT NULL,
  `time_battle` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `cp` int(11) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id` (`external_id`),
  KEY `fort_id` (`fort_id`),
  KEY `ix_raids_time_spawn` (`time_spawn`),
  CONSTRAINT `raids_ibfk_1` FOREIGN KEY (`fort_id`) REFERENCES `forts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.sightings
CREATE TABLE IF NOT EXISTS `sightings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `spawn_id` bigint(20) DEFAULT NULL,
  `expire_timestamp` int(11) DEFAULT NULL,
  `encounter_id` bigint(20) unsigned DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `atk_iv` tinyint(3) unsigned DEFAULT NULL,
  `def_iv` tinyint(3) unsigned DEFAULT NULL,
  `sta_iv` tinyint(3) unsigned DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `gender` smallint(6) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL,
  `cp` smallint(6) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `weather_boosted_condition` smallint(6) DEFAULT NULL,
  `weather_cell_id` bigint(20) unsigned DEFAULT NULL,
  `weight` double(18,14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timestamp_encounter_id_unique` (`encounter_id`,`expire_timestamp`),
  KEY `ix_sightings_encounter_id` (`encounter_id`),
  KEY `ix_sightings_expire_timestamp` (`expire_timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table Monocle.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` varchar(250) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `temp_password` varchar(250) DEFAULT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `login_system` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.
-- Dumping structure for tahle Monocle.communities
CREATE TABLE `communities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_id` varchar(35) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `image_url` varchar(200) DEFAULT NULL,
  `size` smallint(6) DEFAULT NULL,
  `team_instinct` tinyint(4) DEFAULT NULL,
  `team_mystic` tinyint(4) DEFAULT NULL,
  `team_valor` tinyint(4) DEFAULT NULL,
  `has_invite_url` varchar(4) DEFAULT NULL,
  `invite_url` varchar(512) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `source` tinyint(4) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `gym_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fort_id` int(11) DEFAULT NULL,
  `param_1` int(11) DEFAULT NULL,
  `param_2` int(11) DEFAULT NULL,
  `param_3` int(11) DEFAULT NULL,
  `param_4` int(11) DEFAULT NULL,
  `param_5` int(11) DEFAULT NULL,
  `param_6` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL
  PRIMARY KEY (`id`);
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pokemon_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pokemon_id` int(11) DEFAULT NULL,
  `param_1` int(11) DEFAULT NULL,
  `param_2` int(11) DEFAULT NULL,
  `param_3` int(11) DEFAULT NULL,
  `param_4` int(11) DEFAULT NULL,
  `param_5` int(11) DEFAULT NULL,
  `param_6` int(11) DEFAULT NULL,
  `param_7` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL
  PRIMARY KEY (`id`);
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
