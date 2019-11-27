<?php
/*
 * Add 'conditions_serialized' to the oops table
 */


$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()->addColumn($table,'conditions_serialized','mediumtext');

$installer->endSetup();
