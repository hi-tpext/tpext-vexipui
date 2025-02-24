CREATE TABLE IF NOT EXISTS `__PREFIX__attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '后台用户id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '前台用户id',
  `name` varchar(55) NOT NULL DEFAULT '' COMMENT '名称',
  `mime` varchar(125) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `suffix` varchar(10) NOT NULL DEFAULT '' COMMENT '后缀',
  `size` double(10,3) unsigned DEFAULT '0' COMMENT '大小',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT 'sha1',
  `storage` varchar(40) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT 'url链接',
  `create_time` datetime NOT NULL DEFAULT '2020-01-01 00:00:00' COMMENT '添加时间',
  `update_time` datetime NOT NULL DEFAULT '2020-01-01 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  INDEX(`admin_id`),
  INDEX(`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='上传文件表';

/* 
* 小版本升级 ，要修改已存在的表，不能直接修改建表语句，应该以小版本的形式提供升级
* 1. install中添加单独的修改语句，
* 2. 创建版本sql，如`1.0.2.sql`,在其中写入单独的sql语句。
* 对于已安装扩展的用户，将通过各个版本的sql修改表。
* 对于新安装的用户，通过intall.sql执行建表和修改表的操作
*/

/* 1.0.3.sql */
ALTER TABLE `__PREFIX__attachment`
	CHANGE COLUMN `url` `url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'url链接' COLLATE 'utf8_general_ci' AFTER `storage`;