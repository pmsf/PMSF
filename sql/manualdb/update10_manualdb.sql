ALTER TABLE `users` ADD `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `access_level`;
ALTER TABLE `users` ADD `discord_guilds` TEXT NULL DEFAULT NULL AFTER `avatar`;
