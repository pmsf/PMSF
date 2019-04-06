ALTER TABLE `poi` 
ADD COLUMN `notes` VARCHAR(1024) NULL AFTER `description`,
ADD COLUMN `edited_by` VARCHAR(200) NULL AFTER `submitted_by`,
CHANGE COLUMN `description` `description` VARCHAR(1024) NULL DEFAULT NULL ;