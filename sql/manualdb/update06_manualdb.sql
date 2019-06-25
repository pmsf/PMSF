CREATE TABLE IF NOT EXISTS `inn` (
  `id` varchar(35) COLLATE utf8mb4_bin NOT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_bin DEFAULT NULL,
  `url` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `updated` bigint(20) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `fortress` (
  `id` varchar(35) COLLATE utf8mb4_bin NOT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_bin DEFAULT NULL,
  `url` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `greenhouse` (
  `id` varchar(35) COLLATE utf8mb4_bin NOT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_bin DEFAULT NULL,
  `url` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;