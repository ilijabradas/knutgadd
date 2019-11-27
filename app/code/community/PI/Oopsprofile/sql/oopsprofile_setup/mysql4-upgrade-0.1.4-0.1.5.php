<?php



$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()->addColumn($table,'profile_enable','int(5)');

$installer->endSetup();
