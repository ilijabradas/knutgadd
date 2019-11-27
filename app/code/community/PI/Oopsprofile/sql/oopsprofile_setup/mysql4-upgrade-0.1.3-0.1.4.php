<?php



$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

//$installer->getConnection()->addColumn($table,'run_on_product_save','int(5)');

//$installer->getConnection()->addColumn($table,'run_on_category_save','int(5)');

$installer->getConnection()->addColumn($table,'profile_direction','varchar(50)');

$installer->endSetup();
