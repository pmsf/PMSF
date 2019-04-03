CREATE TABLE `poi` (
  `id` int(11) NOT NULL,
  `poi_id` varchar(35) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `lat` double(18,14) DEFAULT NULL,
  `lon` double(18,14) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `submitted_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `poi`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `poi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
