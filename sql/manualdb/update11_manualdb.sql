ALTER TABLE `users` ADD `last_loggedin` INT(11) NULL DEFAULT NULL;
ALTER TABLE `users` ADD `session_token` VARCHAR(255) NULL DEFAULT NULL AFTER `session_id`;
