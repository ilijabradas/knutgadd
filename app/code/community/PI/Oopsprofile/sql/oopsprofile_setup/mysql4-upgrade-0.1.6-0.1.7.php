<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('oopsprofile/mapping')};
CREATE TABLE {$this->getTable('oopsprofile/mapping')} (
  `category_map_id` int(11) unsigned NOT NULL auto_increment,  
  `category_map_name` varchar(255),
  `mapping_detail` varchar(1000),
  PRIMARY KEY (`category_map_id`)
) ENGINE = INNODB CHARSET=utf8;

");
$installer->endSetup(); 
