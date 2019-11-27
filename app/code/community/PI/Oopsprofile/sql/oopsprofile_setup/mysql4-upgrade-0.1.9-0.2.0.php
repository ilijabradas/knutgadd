<?php



$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()->addColumn($table,'xml_content','varchar(10000) default "" ');

$installer->getConnection()->addColumn($table,'enable_content','int(5) default 0');

$installer->endSetup();
