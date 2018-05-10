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
  `login_system` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
ADD (quest_id TINYINT(4), reward VARCHAR(40));
