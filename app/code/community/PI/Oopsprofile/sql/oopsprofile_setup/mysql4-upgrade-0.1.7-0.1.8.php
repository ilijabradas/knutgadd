<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('oopsprofile/template')};
CREATE TABLE {$this->getTable('oopsprofile/template')} (
  `temp_id` int(11) unsigned NOT NULL auto_increment,  
  `temp_name` varchar(255),
  `temp_file_type` varchar(255),
  `fields_mapping_xml` varchar(1000),
  `data_format_xml` varchar(500),
  `gui_data` varchar(5000),
  PRIMARY KEY (`temp_id`)
) ENGINE = INNODB CHARSET=utf8;

");
$installer->endSetup(); 
