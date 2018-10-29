//--------------------------------------
// Everything in this file is known to work with mariadb/mysql. May need slight adjustments for PGSQL.
//--------------------------------------

//--------------------------------------
// Required for Discord Login
//--------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user` varchar(250) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `temp_password` varchar(250) DEFAULT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `login_system` varchar(40) NOT NULL,
  `access_level` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `selly_id` varchar(100) NOT NULL,
  `product_id` int(30) NOT NULL,
  `email` varchar(250) NOT NULL,
  `value` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

//--------------------------------------
// Required for Nests
//--------------------------------------
CREATE TABLE IF NOT EXISTS `nests` (
  `nest_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `pokemon_id` int(11) DEFAULT 0,
  `updated` bigint(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`nest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=323280 DEFAULT CHARSET=utf8;

//--------------------------------------
// Required for Quests
//--------------------------------------
ALTER TABLE pokestops
ADD (quest_id SMALLINT(4), reward_id SMALLINT(4));

//--------------------------------------
// Required for Login & Logging
//--------------------------------------
ALTER TABLE pokestops
ADD (quest_submitted_by VARCHAR(200), edited_by VARCHAR(200));
ALTER TABLE forts
ADD (edited_by VARCHAR(200), submitted_by varchar(200) DEFAULT NULL);
ALTER TABLE nests
ADD nest_submitted_by VARCHAR(200);

ALTER TABLE raids
ADD form smallint(6) DEFAULT NULL;

ALTER TABLE fort_sightings
ADD guard_pokemon_form SMALLINT(6) NULL DEFAULT NULL AFTER guard_pokemon_id;

ALTER TABLE gym_defenders
ADD form SMALLINT(6) NULL DEFAULT NULL AFTER pokemon_id;

