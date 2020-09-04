ALTER TABLE `users` ADD `linked_account` BIGINT(16) NULL AFTER `discord_guilds`;
ALTER TABLE `users` CHANGE `id` `id` VARCHAR(255) NOT NULL;
