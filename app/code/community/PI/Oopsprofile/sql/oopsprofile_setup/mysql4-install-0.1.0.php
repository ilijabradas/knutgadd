<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('oopsprofile/oopsprofile')};
CREATE TABLE {$this->getTable('oopsprofile/oopsprofile')} (
  `oops_profile_id` int(11) unsigned NOT NULL auto_increment,  
  `dataflow_profile_id` int(11) UNSIGNED,
  PRIMARY KEY (`oops_profile_id`)
) ENGINE = INNODB CHARSET=utf8;

");
$installer->endSetup(); 
