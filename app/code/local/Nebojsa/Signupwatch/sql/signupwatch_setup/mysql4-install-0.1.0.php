<?php
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('signupwatch')};
CREATE TABLE {$this->getTable('signupwatch')} (
  `signupwatch_id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `watch_name` varchar(255) NOT NULL default '',
  `watch_belttype` varchar(255) NOT NULL default '',
  `watch_date` varchar(255) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  PRIMARY KEY USING BTREE (`signupwatch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();