<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('news')};
CREATE TABLE {$this->getTable('news')} (
  `news_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `texto` text NOT NULL default '',
  `image_one` varchar(255) NOT NULL default '',
  `image_two` varchar(255) NOT NULL default '',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created At',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY USING BTREE (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
  