SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `communities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_id` varchar(35) COLLATE utf8mb4_bin DEFAULT NULL,
  `title` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL,
  `description` varchar(256) COLLATE utf8mb4_bin DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `image_url` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `size` smallint(6) DEFAULT NULL,
  `team_instinct` tinyint(4) DEFAULT NULL,
  `team_mystic` tinyint(4) DEFAULT NULL,
  `team_valor` tinyint(4) DEFAULT NULL,
  `has_invite_url` varchar(4) COLLATE utf8mb4_bin DEFAULT NULL,
  `invite_url` varchar(512) COLLATE utf8mb4_bin DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `source` tinyint(4) DEFAULT NULL,
  `submitted_by` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `community_id` (`community_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `ingress_portals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(35) COLLATE utf8mb4_bin DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_bin DEFAULT NULL,
  `url` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `updated` bigint(11) NOT NULL,
  `imported` bigint(11) DEFAULT NULL,
  `checked` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id` (`external_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `nests` (
  `nest_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `pokemon_id` int(11) DEFAULT 0,
  `updated` bigint(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `nest_submitted_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`nest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` varchar(250) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `temp_password` varchar(250) DEFAULT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `login_system` varchar(40) NOT NULL,
  `access_level` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
