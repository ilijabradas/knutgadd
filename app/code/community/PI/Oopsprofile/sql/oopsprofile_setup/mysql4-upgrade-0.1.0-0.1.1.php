<?php
/*
 * Add 'service', 'oauth_url' to the oops table
 */


$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()->addColumn($table, 'service_type', 'varchar(255)');
$installer->getConnection()->addColumn($table, 'oauth_url', 'varchar(510)');
$installer->getConnection()->addColumn($table, 'is_token_available', 'smallint(5)');

$installer->endSetup();
