<?php



$installer = $this;

$table = $installer->getTable('oopsprofile/template');

$installer->startSetup();

$installer->getConnection()->addColumn($table,'selected_profile','int(5) default 0 ');

$installer->getConnection()->addColumn($table,'category_mapping','int(5) default 0 ');

$installer->endSetup();
