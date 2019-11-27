<?php
/**
 * Klarna Checkout extension
 *
 * @category    NWT
 * @package     NWT_KCO
 * @copyright   Copyright (c) 2015 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     http://nordicwebteam.se/licenses/nwtcl/1.0.txt  NWT Commercial License (NWTCL 1.0)
 * 
 *
 */

/**
 * upgrade script
 */

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/* var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $this->getConnection();


$pushTable   = $installer->getTable('nwtkco/push');

$installer->run("
CREATE TABLE IF NOT EXISTS `{$pushTable}` (
  `entity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kid` varchar(255) NOT NULL,
  `marshal` LONGBLOB COMMENT 'Klarna order data, serialized',
  `origin` varchar(255) NOT NULL DEFAULT '' COMMENT 'Who create the request, confirmation or push',
  `error`  tinyint(3) NOT NULL,
  `error_msg` TEXT,
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `UNQ_ORDER_ID` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");



$_table     = 'sales/order';
$table      = $installer->getTable($_table);
$column     = 'nwt_kid';
$definition = "int(11) unsigned DEFAULT NULL  COMMENT 'Klarna Push Request ID' AFTER `nwt_reservation`";

$connection->addColumn($table,$column,$definition);
$connection->addIndex($table, 'UNQ_SALES_FLAT_ORDER_NWT_KID', array($column),Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);


$installer->endSetup ();

