<?php



$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()->addColumn($table,'save_local_copy','int(5) default 0');

$installer->getConnection()->addColumn($table,'products_allready_done','int(5) default 1');

$installer->getConnection()->addColumn($table,'save_products_separately','int(5) default 0');

$installer->getConnection()->addColumn($table,'profile_template','int(5) default 0 ');

$installer->endSetup();
