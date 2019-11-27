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

$installer = $this;
$connection = $installer->getConnection();

$productTable               = $installer->getTable('catalog/product');
$productCurrencyPriceTable  = $installer->getTable('catalog/product_currency_price');
$productZonePriceTable      = $installer->getTable('catalog/product_zone_price');
$catalogEavAttributeTable   = $installer->getTable('catalog/eav_attribute');
$eavAttributeTable          = $installer->getTable('eav/attribute');
$eavEntityTypeTable         = $installer->getTable('eav/entity_type');
$storeTable                 = $installer->getTable('core/store');

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$productCurrencyPriceTable}`;

CREATE TABLE `{$productCurrencyPriceTable}` (
  `product_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`product_id`, `currency`, `store_id`), 
  KEY `FK_CATALOG_PRODUCT_CURRENCY_PRICE_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_CURRENCY_PRICE_STORE` (`store_id`), 
  KEY `IDX_CURRENCY` (`currency`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_CURRENCY_PRICE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_CURRENCY_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
DROP TABLE IF EXISTS `{$productZonePriceTable}`;

CREATE TABLE `{$productZonePriceTable}` (
    `zone_price_id` int(10) unsigned not null auto_increment, 
    `product_id` int(10) unsigned not null, 
    `country_id` varchar(4) not null default '0', 
    `region_id` int(10) not null default '0', 
    `zip` varchar(10) not null default '', 
    `price` decimal(12,4) NOT NULL default '0.00', 
    `price_type` enum('fixed', 'percent') NOT NULL default 'fixed', 
    PRIMARY KEY  (`zone_price_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_COUNTRY` (`country_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_REGION` (`region_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_ZIP` (`zip`), 
    KEY `FK_CATALOG_PRODUCT_ZONE_PRICE_PRODUCT` (`product_id`), 
    CONSTRAINT `FK_CATALOG_PRODUCT_ZONE_PRICE_PRODUCT` FOREIGN KEY (`product_id`) 
        REFERENCES {$productTable} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Price index
 */
$productIndexPriceTable = $installer->getTable('catalog/product_index_price');
$connection->addColumn($productIndexPriceTable, 'currency', 'varchar(3) null default null');
$connection->addKey($productIndexPriceTable, 'IDX_CURRENCY', array('currency'), 'index');
$connection->addColumn($productIndexPriceTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceTable, 'IDX_CATALOG_PRODUCT_INDEX_PRICE_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOG_PRODUCT_INDEX_PRICE_STORE_ID', $productIndexPriceTable, 'store_id', $storeTable, 'store_id');
$connection->addKey($productIndexPriceTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');

$productIndexPriceIdxTable = $installer->getTable('catalog/product_price_indexer_idx');
$connection->addColumn($productIndexPriceIdxTable, 'currency', 'varchar(3) null default null');
$connection->addKey($productIndexPriceIdxTable, 'IDX_CURRENCY', array('currency'), 'index');
$connection->addColumn($productIndexPriceIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceIdxTable, 'IDX_CATALOG_PRODUCT_INDEX_PRICE_IDX_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOG_PRODUCT_INDEX_PRICE_IDX_STORE_ID', $productIndexPriceIdxTable, 'store_id', $storeTable, 'store_id');
$connection->addKey($productIndexPriceIdxTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');

$productIndexPriceTmpTable = $installer->getTable('catalog/product_price_indexer_tmp');
$connection->addColumn($productIndexPriceTmpTable, 'currency', 'varchar(3) null default null');
$connection->addKey($productIndexPriceTmpTable, 'IDX_CURRENCY', array('currency'), 'index');
$connection->addColumn($productIndexPriceTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceTmpTable, 'IDX_CATALOG_PRODUCT_INDEX_PRICE_TMP_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOG_PRODUCT_INDEX_PRICE_TMP_STORE_ID', $productIndexPriceTmpTable, 'store_id', $storeTable, 'store_id');
$connection->addKey($productIndexPriceTmpTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');
/**
 * Final price index
 */
$productIndexPriceFinalIdxTable = $installer->getTable('catalog/product_price_indexer_final_idx');
$connection->addColumn($productIndexPriceFinalIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceFinalIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceFinalIdxTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');

$productIndexPriceFinalTmpTable = $installer->getTable('catalog/product_price_indexer_final_tmp');
$connection->addColumn($productIndexPriceFinalTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceFinalTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceFinalTmpTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');
/**
 * Bundle price index
 */
$productIndexPriceBundleIdxTable = $installer->getTable('bundle/price_indexer_idx');
$connection->addColumn($productIndexPriceBundleIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleIdxTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');

$productIndexPriceBundleTmpTable = $installer->getTable('bundle/price_indexer_tmp');
$connection->addColumn($productIndexPriceBundleTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleTmpTable, 'PRIMARY', array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 'primary');

$productIndexPriceBundleSelectionIdxTable = $installer->getTable('bundle/selection_indexer_idx');
$connection->addColumn($productIndexPriceBundleSelectionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleSelectionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleSelectionIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'selection_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceBundleSelectionTmpTable = $installer->getTable('bundle/selection_indexer_tmp');
$connection->addColumn($productIndexPriceBundleSelectionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleSelectionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleSelectionTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'selection_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceBundleOptionIdxTable = $installer->getTable('bundle/option_indexer_idx');
$connection->addColumn($productIndexPriceBundleOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleOptionIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceBundleOptionTmpTable = $installer->getTable('bundle/option_indexer_tmp');
$connection->addColumn($productIndexPriceBundleOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceBundleOptionTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'
), 'primary');
/**
 * Option price index
 */
$productIndexPriceOptionAggregateIdxTable = $installer->getTable('catalog/product_price_indexer_option_aggregate_idx');
$connection->addColumn($productIndexPriceOptionAggregateIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionAggregateIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceOptionAggregateIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceOptionAggregateTmpTable = $installer->getTable('catalog/product_price_indexer_option_aggregate_tmp');
$connection->addColumn($productIndexPriceOptionAggregateTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionAggregateTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceOptionAggregateTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceOptionIdxTable = $installer->getTable('catalog/product_price_indexer_option_idx');
$connection->addColumn($productIndexPriceOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceOptionIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceOptionTmpTable = $installer->getTable('catalog/product_price_indexer_option_tmp');
$connection->addColumn($productIndexPriceOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceOptionTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');
/**
 * Downloadable price index
 */
$productIndexPriceDownloadableIdxTable = $installer->getTable('downloadable/product_price_indexer_idx');
$connection->addColumn($productIndexPriceDownloadableIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceDownloadableIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceDownloadableIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceDownloadableTmpTable = $installer->getTable('downloadable/product_price_indexer_tmp');
$connection->addColumn($productIndexPriceDownloadableTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceDownloadableTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceDownloadableTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');
/**
 * Configurable option price index
 */
$productIndexPriceCfgOptionAggregateIdxTable = $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx');
$connection->addColumn($productIndexPriceCfgOptionAggregateIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionAggregateIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceCfgOptionAggregateIdxTable, 'PRIMARY', array(
    'parent_id', 'child_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceCfgOptionAggregateTmpTable = $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp');
$connection->addColumn($productIndexPriceCfgOptionAggregateTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionAggregateTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceCfgOptionAggregateTmpTable, 'PRIMARY', array(
    'parent_id', 'child_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceCfgOptionIdxTable = $installer->getTable('catalog/product_price_indexer_cfg_option_idx');
$connection->addColumn($productIndexPriceCfgOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceCfgOptionIdxTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$productIndexPriceCfgOptionTmpTable = $installer->getTable('catalog/product_price_indexer_cfg_option_tmp');
$connection->addColumn($productIndexPriceCfgOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey($productIndexPriceCfgOptionTmpTable, 'PRIMARY', array(
    'entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'
), 'primary');

$installer->endSetup();
