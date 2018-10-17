SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `common` (
  `id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `val` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `communities` (
  `id` int(11) NOT NULL,
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

CREATE TABLE `forts` (
  `id` int(11) NOT NULL,
  `external_id` varchar(35) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `sponsor` smallint(6) DEFAULT NULL,
  `weather_cell_id` bigint(20) UNSIGNED DEFAULT NULL,
  `park` varchar(128) DEFAULT NULL,
  `parkid` bigint(20) DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `fort_sightings` (
  `id` bigint(20) NOT NULL,
  `fort_id` int(11) DEFAULT NULL,
  `last_modified` int(11) DEFAULT NULL,
  `team` tinyint(3) UNSIGNED DEFAULT NULL,
  `guard_pokemon_id` smallint(6) DEFAULT NULL,
  `slots_available` smallint(6) DEFAULT NULL,
  `is_in_battle` tinyint(1) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `gym_defenders` (
  `id` bigint(20) NOT NULL,
  `fort_id` int(11) NOT NULL,
  `external_id` bigint(20) UNSIGNED NOT NULL,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `team` tinyint(3) UNSIGNED DEFAULT NULL,
  `owner_name` varchar(128) DEFAULT NULL,
  `nickname` varchar(128) DEFAULT NULL,
  `cp` int(11) DEFAULT NULL,
  `stamina` int(11) DEFAULT NULL,
  `stamina_max` int(11) DEFAULT NULL,
  `atk_iv` smallint(6) DEFAULT NULL,
  `def_iv` smallint(6) DEFAULT NULL,
  `sta_iv` smallint(6) DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `last_modified` int(11) DEFAULT NULL,
  `battles_attacked` int(11) DEFAULT NULL,
  `battles_defended` int(11) DEFAULT NULL,
  `num_upgrades` smallint(6) DEFAULT NULL,
  `created` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `ingress_portals` (
  `id` int(11) NOT NULL,
  `external_id` varchar(35) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `updated` bigint(11) NOT NULL,
  `imported` bigint(11) DEFAULT NULL,
  `checked` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mystery_sightings` (
  `id` bigint(20) NOT NULL,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `spawn_id` bigint(20) DEFAULT NULL,
  `encounter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `first_seen` int(11) DEFAULT NULL,
  `first_seconds` smallint(6) DEFAULT NULL,
  `last_seconds` smallint(6) DEFAULT NULL,
  `seen_range` smallint(6) DEFAULT NULL,
  `atk_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `def_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `sta_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `gender` smallint(6) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL,
  `cp` smallint(6) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `weather_boosted_condition` smallint(6) DEFAULT NULL,
  `weather_cell_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `nests` (
  `nest_id` bigint(20) NOT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `pokemon_id` int(11) DEFAULT 0,
  `updated` bigint(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `nest_submitted_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `selly_id` varchar(100) NOT NULL,
  `product_id` int(30) NOT NULL,
  `email` varchar(250) NOT NULL,
  `value` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pokestops` (
  `id` int(11) NOT NULL,
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
  `edited_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `raids` (
  `id` int(11) NOT NULL,
  `external_id` bigint(20) DEFAULT NULL,
  `fort_id` int(11) DEFAULT NULL,
  `level` tinyint(3) UNSIGNED DEFAULT NULL,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `time_spawn` int(11) DEFAULT NULL,
  `time_battle` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `cp` int(11) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sightings` (
  `id` bigint(20) NOT NULL,
  `pokemon_id` smallint(6) DEFAULT NULL,
  `spawn_id` bigint(20) DEFAULT NULL,
  `expire_timestamp` int(11) DEFAULT NULL,
  `encounter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `atk_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `def_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `sta_iv` tinyint(3) UNSIGNED DEFAULT NULL,
  `move_1` smallint(6) DEFAULT NULL,
  `move_2` smallint(6) DEFAULT NULL,
  `gender` smallint(6) DEFAULT NULL,
  `form` smallint(6) DEFAULT NULL,
  `cp` smallint(6) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `weather_boosted_condition` smallint(6) DEFAULT NULL,
  `weather_cell_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weight` double(18,14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `spawnpoints` (
  `id` int(11) NOT NULL,
  `spawn_id` bigint(20) DEFAULT NULL,
  `despawn_time` smallint(6) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `duration` tinyint(3) UNSIGNED DEFAULT NULL,
  `failures` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user` varchar(250) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `temp_password` varchar(250) DEFAULT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `login_system` varchar(40) NOT NULL,
  `access_level` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `weather` (
  `id` int(11) NOT NULL,
  `s2_cell_id` bigint(20) DEFAULT NULL,
  `condition` tinyint(3) UNSIGNED DEFAULT NULL,
  `alert_severity` tinyint(3) UNSIGNED DEFAULT NULL,
  `warn` tinyint(1) DEFAULT NULL,
  `day` tinyint(3) UNSIGNED DEFAULT NULL,
  `updated` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `common`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_common_key` (`key`);

ALTER TABLE `communities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `community_id` (`community_id`);

ALTER TABLE `forts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_id` (`external_id`),
  ADD KEY `ix_coords` (`lat`,`lon`);

ALTER TABLE `fort_sightings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fort_id_last_modified_unique` (`fort_id`,`last_modified`),
  ADD UNIQUE KEY `fort_id` (`fort_id`),
  ADD KEY `ix_fort_sightings_last_modified` (`last_modified`);

ALTER TABLE `gym_defenders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_gym_defenders_fort_id` (`fort_id`),
  ADD KEY `ix_gym_defenders_created` (`created`);

ALTER TABLE `ingress_portals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_id` (`external_id`);

ALTER TABLE `mystery_sightings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_encounter` (`encounter_id`,`spawn_id`),
  ADD KEY `ix_mystery_sightings_encounter_id` (`encounter_id`),
  ADD KEY `ix_mystery_sightings_spawn_id` (`spawn_id`),
  ADD KEY `ix_mystery_sightings_first_seen` (`first_seen`);

ALTER TABLE `nests`
  ADD PRIMARY KEY (`nest_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pokestops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_id` (`external_id`),
  ADD KEY `ix_pokestops_lon` (`lon`),
  ADD KEY `ix_pokestops_lat` (`lat`);

ALTER TABLE `raids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_id` (`external_id`),
  ADD KEY `fort_id` (`fort_id`),
  ADD KEY `ix_raids_time_spawn` (`time_spawn`);

ALTER TABLE `sightings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `timestamp_encounter_id_unique` (`encounter_id`,`expire_timestamp`),
  ADD KEY `ix_sightings_encounter_id` (`encounter_id`),
  ADD KEY `ix_sightings_expire_timestamp` (`expire_timestamp`);

ALTER TABLE `spawnpoints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ix_spawnpoints_spawn_id` (`spawn_id`),
  ADD KEY `ix_spawnpoints_updated` (`updated`),
  ADD KEY `ix_coords_sp` (`lat`,`lon`),
  ADD KEY `ix_spawnpoints_despawn_time` (`despawn_time`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `weather`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `common`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `communities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;
ALTER TABLE `forts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133192;
ALTER TABLE `fort_sightings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131404;
ALTER TABLE `gym_defenders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ingress_portals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102721;
ALTER TABLE `mystery_sightings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nests`
  MODIFY `nest_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360612;
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pokestops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=466716;
ALTER TABLE `raids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133862;
ALTER TABLE `sightings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3170831;
ALTER TABLE `spawnpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=497709947086176257;
ALTER TABLE `weather`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `fort_sightings`
  ADD CONSTRAINT `fort_sightings_ibfk_1` FOREIGN KEY (`fort_id`) REFERENCES `forts` (`id`);

ALTER TABLE `gym_defenders`
  ADD CONSTRAINT `gym_defenders_ibfk_1` FOREIGN KEY (`fort_id`) REFERENCES `forts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `raids`
  ADD CONSTRAINT `raids_ibfk_1` FOREIGN KEY (`fort_id`) REFERENCES `forts` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
