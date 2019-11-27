<?php


$installer = $this;

$table = $installer->getTable('oopsprofile/oopsprofile');

$installer->startSetup();

$installer->getConnection()
					->addConstraint(
						  'FK_ITEMS_RELATION_ITEM',
						  $table, 
						  'dataflow_profile_id',
						  $installer->getTable('dataflow/profile'), 
						  'profile_id',
						  'cascade', 
						  'cascade'
					);

$installer->endSetup();
