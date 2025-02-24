ALTER TABLE `__PREFIX__attachment`
	CHANGE COLUMN `url` `url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'url链接' COLLATE 'utf8_general_ci' AFTER `storage`;