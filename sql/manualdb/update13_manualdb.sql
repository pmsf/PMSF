ALTER TABLE `nests`
ADD `pokemon_form` smallint(6) DEFAULT NULL,
ADD `pokemon_ratio` double DEFAULT 0,

ADD `polygon_type` tinyint(1) DEFAULT 0,
ADD `polygon_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

ADD `unknown_name` tinyint(1) DEFAULT 0,

DROP `nest_submitted_by`;
