ALTER TABLE `users` ADD `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `access_level`;
ALTER TABLE `users` ADD `discord_guilds` TEXT NULL DEFAULT NULL AFTER `avatar`;
ALTER TABLE `users` CHANGE `session_id` `session_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
