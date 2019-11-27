<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedPricing
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

$installer                                = $this;
$connection                               = $installer->getConnection();

$installer->startSetup();

$storeTable                               = $installer->getTable('core/store');

/* Product Tier Price */
$productTierPriceTableName                = 'catalog/product_attribute_tier_price';
$productTierPriceTable                    = $installer->getTable($productTierPriceTableName);

$connection->addColumn($productTierPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
$connection->addKey($productTierPriceTable, 'IDX_CATALOG_PRODUCT_ENTITY_TIER_PRICE_CURRENCY', array('currency'), 'index');

$connection->addColumn($productTierPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
$connection->addKey($productTierPriceTable, 'IDX_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE_ID', $productTierPriceTable, 'store_id', $storeTable, 'store_id');

if (Mage::helper('advancedpricing')->getVersionHelper()->isGe1600()) {
    $productTierPriceIndexes = $connection->getIndexList($productTierPriceTable);
    foreach ($productTierPriceIndexes as $index) {
        if ($index['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
            $connection->dropIndex($productTierPriceTable, $index['KEY_NAME']);
        }
    }
    $connection->addIndex(
        $productTierPriceTable, 
        $installer->getIdxName(
            $productTierPriceTableName, 
            array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id', 'currency', 'store_id'), 
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id', 'currency', 'store_id'), 
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
} else {
    $connection->addKey($productTierPriceTable, 'UNQ_CATALOG_PRODUCT_TIER_PRICE', array(
        'entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id', 'currency', 'store_id', 
    ), 'unique');
}
/* Product Index Tier Price */
$productIndexTierPriceTableName     = 'catalog/product_index_tier_price';
$productIndexTierPriceTable         = $installer->getTable($productIndexTierPriceTableName);

$connection->addColumn($productIndexTierPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
$connection->addKey($productIndexTierPriceTable, 'IDX_CATALOG_PRODUCT_INDEX_TIER_PRICE_CURRENCY', array('currency'), 'index');

$connection->addColumn($productIndexTierPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
$connection->addKey($productIndexTierPriceTable, 'IDX_CATALOG_PRODUCT_INDEX_TIER_PRICE_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_STORE_ID', $productIndexTierPriceTable, 'store_id', $storeTable, 'store_id');

$connection->addKey($productIndexTierPriceTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');
/* Product Currency Special Price */
$productTable                       = $installer->getTable('catalog/product');
$productCurrencySpecialPriceTable   = $installer->getTable('catalog/product_currency_special_price');

$installer->run("
CREATE TABLE `{$productCurrencySpecialPriceTable}` (
  `product_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`product_id`, `currency`, `store_id`), 
  KEY `FK_CATALOG_PRODUCT_CURRENCY_SPECIAL_PRICE_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_CURRENCY_SPECIAL_PRICE_STORE` (`store_id`), 
  KEY `IDX_CURRENCY` (`currency`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_CURRENCY_SPECIAL_PRICE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_CURRENCY_SPECIAL_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
/* Product Zone Price */
$productZonePriceTable              = $installer->getTable('catalog/product_zone_price');

$connection->changeColumn($productZonePriceTable, 'zip', 'zip', 'varchar(21) null default null');
$connection->addColumn($productZonePriceTable, 'is_zip_range', 'tinyint(1) unsigned not null default 0 after `zip`');
$connection->addColumn($productZonePriceTable, 'from_zip', 'int(10) unsigned null default null after `is_zip_range`');
$connection->addKey($productZonePriceTable, 'IDX_CATALOG_PRODUCT_ZONE_PRICE_FROM_ZIP', array('from_zip'), 'index');
$connection->addColumn($productZonePriceTable, 'to_zip', 'int(10) unsigned null default null after `from_zip`');
$connection->addKey($productZonePriceTable, 'IDX_CATALOG_PRODUCT_ZONE_PRICE_TO_ZIP', array('to_zip'), 'index');
/* EAV Attribute */
$eavAttributeTable                  = $installer->getTable('eav/attribute');
$eavEntityTypeTable                 = $installer->getTable('eav/entity_type');

$installer->run("UPDATE `{$eavAttributeTable}` 
SET `backend_model` = 'catalog/product_attribute_backend_finishdate' 
WHERE (`attribute_code` = 'special_to_date') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `{$eavEntityTypeTable}` WHERE `entity_type_code` = 'catalog_product'
))");

$installer->endSetup();
