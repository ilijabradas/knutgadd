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

$installer                                      = $this;

$connection                                     = $installer->getConnection();

$helper                                         = Mage::helper('advancedpricing');
$versionHelper                                  = $helper->getVersionHelper();
$databaseHelper                                 = $helper->getCoreHelper()->getDatabaseHelper();

$installer->startSetup();

$productTable                                   = $installer->getTable('catalog/product');
$storeTable                                     = $installer->getTable('core/store');
$customerGroupTable                             = $installer->getTable('customer/customer_group');

$eavAttributeTable                              = $installer->getTable('eav/attribute');
$eavEntityTypeTable                             = $installer->getTable('eav/entity_type');

$productCompoundPriceTable                      = $installer->getTable('catalog/product_compound_price');
$productCompoundSpecialPriceTable               = $installer->getTable('catalog/product_compound_special_price');
$productIndexCompoundPriceTable                 = $installer->getTable('catalog/product_index_compound_price');
$productIndexCompoundSpecialPriceTable          = $installer->getTable('catalog/product_index_compound_special_price');

$productTierPriceTableName                      = 'catalog/product_attribute_tier_price';
$productTierPriceTable                          = $installer->getTable($productTierPriceTableName);
$productIndexTierPriceTableName                 = 'catalog/product_index_tier_price';
$productIndexTierPriceTable                     = $installer->getTable($productIndexTierPriceTableName);

if ($versionHelper->isGe1700()) {
    $productGroupPriceTableName                     = 'catalog/product_attribute_group_price';
    $productGroupPriceTable                         = $installer->getTable($productGroupPriceTableName);
    $productIndexGroupPriceTableName                = 'catalog/product_index_group_price';
    $productIndexGroupPriceTable                    = $installer->getTable($productIndexGroupPriceTableName);
}

$productZonePriceTable                          = $installer->getTable('catalog/product_zone_price');

$catalogRuleTable                               = $installer->getTable('catalogrule/rule');
$catalogRuleCompoundDiscountAmountTable         = $installer->getTable('catalogrule/compound_discount_amount');
$catalogRuleCompoundSubDiscountAmountTable      = $installer->getTable('catalogrule/compound_sub_discount_amount');

if ($versionHelper->isGe1700()) {
    $catalogRuleCurrencyTable                       = $installer->getTable('catalogrule/currency');
    $catalogRuleStoreTable                          = $installer->getTable('catalogrule/store');
}

$catalogRuleGroupStoreTable                     = $installer->getTable('catalogrule/rule_group_store');
$catalogRuleProductTableName                    = 'catalogrule/rule_product';
$catalogRuleProductTable                        = $installer->getTable($catalogRuleProductTableName);
$catalogRuleProductPriceTableName               = 'catalogrule/rule_product_price';
$catalogRuleProductPriceTable                   = $installer->getTable($catalogRuleProductPriceTableName);

$productIndexPriceTable                         = $installer->getTable('catalog/product_index_price');
$productIndexPriceIdxTable                      = $installer->getTable('catalog/product_price_indexer_idx');
$productIndexPriceTmpTable                      = $installer->getTable('catalog/product_price_indexer_tmp');
$productIndexPriceFinalIdxTable                 = $installer->getTable('catalog/product_price_indexer_final_idx');
$productIndexPriceFinalTmpTable                 = $installer->getTable('catalog/product_price_indexer_final_tmp');
$productIndexPriceBundleIdxTable                = $installer->getTable('bundle/price_indexer_idx');
$productIndexPriceBundleTmpTable                = $installer->getTable('bundle/price_indexer_tmp');
$productIndexPriceBundleSelectionIdxTable       = $installer->getTable('bundle/selection_indexer_idx');
$productIndexPriceBundleSelectionTmpTable       = $installer->getTable('bundle/selection_indexer_tmp');
$productIndexPriceBundleOptionIdxTable          = $installer->getTable('bundle/option_indexer_idx');
$productIndexPriceBundleOptionTmpTable          = $installer->getTable('bundle/option_indexer_tmp');
$productIndexPriceOptionAggregateIdxTable       = $installer->getTable('catalog/product_price_indexer_option_aggregate_idx');
$productIndexPriceOptionAggregateTmpTable       = $installer->getTable('catalog/product_price_indexer_option_aggregate_tmp');
$productIndexPriceOptionIdxTable                = $installer->getTable('catalog/product_price_indexer_option_idx');
$productIndexPriceOptionTmpTable                = $installer->getTable('catalog/product_price_indexer_option_tmp');
$productIndexPriceDownloadableIdxTable          = $installer->getTable('downloadable/product_price_indexer_idx');
$productIndexPriceDownloadableTmpTable          = $installer->getTable('downloadable/product_price_indexer_tmp');
$productIndexPriceCfgOptionAggregateIdxTable    = $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx');
$productIndexPriceCfgOptionAggregateTmpTable    = $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp');
$productIndexPriceCfgOptionIdxTable             = $installer->getTable('catalog/product_price_indexer_cfg_option_idx');
$productIndexPriceCfgOptionTmpTable             = $installer->getTable('catalog/product_price_indexer_cfg_option_tmp');

/**
 * Product Compound Price
 */
$installer->run("
CREATE TABLE `{$productCompoundPriceTable}` (
  `product_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`product_id`, `currency`, `store_id`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_PRICE_PRODUCT_ID` (`product_id`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_PRICE_CURRENCY` (`currency`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_PRICE_STORE_ID` (`store_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_COMPOUND_PRICE_PRODUCT_ID` 
    FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_COMPOUND_PRICE_STORE_ID` 
    FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
/**
 * Product Compound Special Price
 */
$installer->run("
CREATE TABLE `{$productCompoundSpecialPriceTable}` (
  `product_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`product_id`, `currency`, `store_id`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_SPECIAL_PRICE_PRODUCT_ID` (`product_id`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_SPECIAL_PRICE_CURRENCY` (`currency`), 
  KEY `IDX_CATALOG_PRODUCT_COMPOUND_SPECIAL_PRICE_STORE_ID` (`store_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_COMPOUND_SPECIAL_PRICE_PRODUCT_ID` 
    FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_COMPOUND_SPECIAL_PRICE_STORE_ID` 
    FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Product Index Compound Price
 */
$installer->run("
CREATE TABLE `{$productIndexCompoundPriceTable}` (
  `entity_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`entity_id`, `currency`, `store_id`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_PRICE_ENTITY_ID` (`entity_id`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_PRICE_CURRENCY` (`currency`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_PRICE_STORE_ID` (`store_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_COMPOUND_PRICE_ENTITY_ID` 
    FOREIGN KEY (`entity_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_COMPOUND_PRICE_STORE_ID` 
    FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Product Index Compound Special Price
 */
$installer->run("
CREATE TABLE `{$productIndexCompoundSpecialPriceTable}` (
  `entity_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `store_id` smallint(5) unsigned not null default 0, 
  `price` decimal(12,4) null default null, 
  PRIMARY KEY  (`entity_id`, `currency`, `store_id`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_SPECIAL_PRICE_ENTITY_ID` (`entity_id`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_SPECIAL_PRICE_CURRENCY` (`currency`), 
  KEY `IDX_CATALOG_PRODUCT_INDEX_COMPOUND_SPECIAL_PRICE_STORE_ID` (`store_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_COMPOUND_SPECIAL_PRICE_ENTITY_ID` 
    FOREIGN KEY (`entity_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_COMPOUND_SPECIAL_PRICE_STORE_ID` 
    FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Product Tier Price
 */
$connection->addColumn($productTierPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
$connection->addKey(
    $productTierPriceTable, 
    'IDX_CATALOG_PRODUCT_ENTITY_TIER_PRICE_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($productTierPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
$connection->addKey(
    $productTierPriceTable, 
    'IDX_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE_ID', 
    $productTierPriceTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$databaseHelper->replaceUniqueKey(
    $installer, $productTierPriceTableName, 'UNQ_CATALOG_PRODUCT_TIER_PRICE', array(
        'entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id', 'currency', 'store_id', 
    )
);

/**
 * Product Index Tier Price
 */
$connection->addColumn($productIndexTierPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
$connection->addKey(
    $productIndexTierPriceTable, 
    'IDX_CATALOG_PRODUCT_INDEX_TIER_PRICE_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($productIndexTierPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
$connection->addKey(
    $productIndexTierPriceTable, 
    'IDX_CATALOG_PRODUCT_INDEX_TIER_PRICE_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_STORE_ID', 
    $productIndexTierPriceTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$connection->addKey(
    $productIndexTierPriceTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Group Price
 */
if ($versionHelper->isGe1700()) {
    $connection->addColumn($productGroupPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
    $connection->addKey(
        $productGroupPriceTable, 
        'IDX_CATALOG_PRODUCT_ENTITY_GROUP_PRICE_CURRENCY', 
        array('currency'), 
        'index'
    );
    $connection->addColumn($productGroupPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
    $connection->addKey(
        $productGroupPriceTable, 
        'IDX_CATALOG_PRODUCT_ENTITY_GROUP_PRICE_STORE_ID', 
        array('store_id'), 
        'index'
    );
    $connection->addConstraint(
        'FK_CATALOG_PRODUCT_ENTITY_GROUP_PRICE_STORE_ID', 
        $productGroupPriceTable, 
        'store_id', 
        $storeTable, 
        'store_id'
    );
    $databaseHelper->replaceUniqueKey(
        $installer, $productGroupPriceTableName, 'UNQ_CATALOG_PRODUCT_GROUP_PRICE', array(
            'entity_id', 'all_groups', 'customer_group_id', 'website_id', 'currency', 'store_id', 
        )
    );
}

/**
 * Product Index Group Price
 */
if ($versionHelper->isGe1700()) {
    $connection->addColumn($productIndexGroupPriceTable, 'currency', 'varchar(3) null default null after `website_id`');
    $connection->addKey(
        $productIndexGroupPriceTable, 
        'IDX_CATALOG_PRODUCT_INDEX_GROUP_PRICE_CURRENCY', 
        array('currency'), 
        'index'
    );
    $connection->addColumn($productIndexGroupPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `currency`');
    $connection->addKey(
        $productIndexGroupPriceTable, 
        'IDX_CATALOG_PRODUCT_INDEX_GROUP_PRICE_STORE_ID', 
        array('store_id'), 
        'index'
    );
    $connection->addConstraint(
        'FK_CATALOG_PRODUCT_INDEX_GROUP_PRICE_STORE_ID', 
        $productIndexGroupPriceTable, 
        'store_id', 
        $storeTable, 
        'store_id'
    );
    $connection->addKey(
        $productIndexGroupPriceTable, 
        'PRIMARY', 
        array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
        'primary'
    );
}

/**
 * Product Zone Price
 */
$installer->run("
CREATE TABLE `{$productZonePriceTable}` (
    `zone_price_id` int(10) unsigned not null auto_increment, 
    `product_id` int(10) unsigned not null, 
    `country_id` varchar(4) not null default '0', 
    `region_id` int(10) not null default '0', 
    `zip` varchar(21) null default null, 
    `is_zip_range` tinyint(1) unsigned not null default 0, 
    `from_zip` int(10) unsigned null default null, 
    `to_zip` int(10) unsigned null default null, 
    `price` decimal(12,4) NOT NULL default '0.00', 
    `price_type` enum('fixed', 'percent') NOT NULL default 'fixed', 
    PRIMARY KEY  (`zone_price_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_COUNTRY` (`country_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_REGION` (`region_id`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_ZIP` (`zip`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_FROM_ZIP` (`from_zip`), 
    KEY `IDX_CATALOG_PRODUCT_ZONE_PRICE_TO_ZIP` (`to_zip`), 
    KEY `FK_CATALOG_PRODUCT_ZONE_PRICE_PRODUCT` (`product_id`), 
    CONSTRAINT `FK_CATALOG_PRODUCT_ZONE_PRICE_PRODUCT` FOREIGN KEY (`product_id`) 
      REFERENCES {$productTable} (`entity_id`) 
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Catalog Rule
 */
if (!$versionHelper->isGe1700()) {
    $connection->addColumn($catalogRuleTable, 'store_ids', 'text null default null after `website_ids`');
    $connection->addColumn($catalogRuleTable, 'currencies', 'text null default null after `store_ids`');
}
    
/**
 * Catalog Rule Compound Discount Amount
 */
$installer->run("
CREATE TABLE `{$catalogRuleCompoundDiscountAmountTable}` (
  `rule_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `amount` decimal(12,4) null default null, 
  PRIMARY KEY  (`rule_id`, `currency`), 
  KEY `IDX_CATALOGRULE_COMPOUND_DISCOUNT_AMOUNT_RULE_ID` (`rule_id`), 
  KEY `IDX_CATALOGRULE_COMPOUND_DISCOUNT_AMOUNT_CURRENCY` (`currency`), 
  CONSTRAINT `FK_CATALOGRULE_COMPOUND_DISCOUNT_AMOUNT_RULE_ID` 
    FOREIGN KEY (`rule_id`) REFERENCES {$catalogRuleTable} (`rule_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
/**
 * Catalog Rule Compound Sub Discount Amount
 */
$installer->run("
CREATE TABLE `{$catalogRuleCompoundSubDiscountAmountTable}` (
  `rule_id` int(10) unsigned not null, 
  `currency` varchar(3) not null, 
  `amount` decimal(12,4) null default null, 
  PRIMARY KEY  (`rule_id`, `currency`), 
  KEY `IDX_CATALOGRULE_COMPOUND_SUB_DISCOUNT_AMOUNT_RULE_ID` (`rule_id`), 
  KEY `IDX_CATALOGRULE_COMPOUND_SUB_DISCOUNT_AMOUNT_CURRENCY` (`currency`), 
  CONSTRAINT `FK_CATALOGRULE_COMPOUND_SUB_DISCOUNT_AMOUNT_RULE_ID` 
    FOREIGN KEY (`rule_id`) REFERENCES {$catalogRuleTable} (`rule_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Catalog Rule Group Store
 */
$installer->run("
CREATE TABLE `{$catalogRuleGroupStoreTable}` (
  `rule_id` int(10) unsigned not null, 
  `customer_group_id` smallint(5) unsigned not null, 
  `store_id` smallint(5) unsigned not null, 
  PRIMARY KEY  (`rule_id`, `customer_group_id`, `store_id`), 
  KEY `IDX_CATALOGRULE_GROUP_STORE_RULE_ID` (`rule_id`), 
  KEY `IDX_CATALOGRULE_GROUP_STORE_CUSTOMER_GROUP_ID` (`customer_group_id`), 
  KEY `IDX_CATALOGRULE_GROUP_STORE_STORE_ID` (`store_id`), 
  CONSTRAINT `FK_CATALOGRULE_GROUP_STORE_RULE_ID` 
    FOREIGN KEY (`rule_id`) REFERENCES {$catalogRuleTable} (`rule_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOGRULE_GROUP_STORE_CUSTOMER_GROUP_ID` 
    FOREIGN KEY (`customer_group_id`) REFERENCES {$customerGroupTable} (`customer_group_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOGRULE_GROUP_STORE_STORE_ID` 
    FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Catalog Rule Store
 */
if ($versionHelper->isGe1700()) {
    $installer->run("
    CREATE TABLE `{$catalogRuleStoreTable}` (
      `rule_id` int(10) unsigned not null, 
      `store_id` smallint(5) unsigned not null, 
      PRIMARY KEY  (`rule_id`, `store_id`), 
      KEY `IDX_CATALOGRULE_STORE_RULE_ID` (`rule_id`), 
      KEY `IDX_CATALOGRULE_STORE_STORE_ID` (`store_id`), 
      CONSTRAINT `FK_CATALOGRULE_STORE_RULE_ID` 
        FOREIGN KEY (`rule_id`) REFERENCES {$catalogRuleTable} (`rule_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE, 
      CONSTRAINT `FK_CATALOGRULE_STORE_STORE_ID` 
        FOREIGN KEY (`store_id`) REFERENCES {$storeTable} (`store_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

/**
 * Catalog Rule Currency
 */
if ($versionHelper->isGe1700()) {
    $installer->run("
    CREATE TABLE `{$catalogRuleCurrencyTable}` (
      `rule_id` int(10) unsigned not null, 
      `currency` varchar(3) not null, 
      PRIMARY KEY  (`rule_id`, `currency`), 
      KEY `IDX_CATALOGRULE_CURRENCY_RULE_ID` (`rule_id`), 
      KEY `IDX_CATALOGRULE_CURRENCY_CURRENCY` (`currency`), 
      CONSTRAINT `FK_CATALOGRULE_CURRENCY_RULE_ID` 
        FOREIGN KEY (`rule_id`) REFERENCES {$catalogRuleTable} (`rule_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

/**
 * Catalog Rule Product
 */
$connection->addColumn($catalogRuleProductTable, 'currency', 'varchar(3) null default null after `product_id`');
$connection->addKey(
    $catalogRuleProductTable, 
    'IDX_CATALOGRULE_PRODUCT_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($catalogRuleProductTable, 'store_id', 'smallint(5) unsigned not null default 0 after `website_id`');
$connection->addKey(
    $catalogRuleProductTable, 
    'IDX_CATALOGRULE_PRODUCT_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOGRULE_PRODUCT_STORE_ID', 
    $catalogRuleProductTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$databaseHelper->replaceUniqueKey(
    $installer, 
    $catalogRuleProductTableName, 
    'sort_order', 
    array('rule_id', 'from_time', 'to_time', 'website_id', 'store_id', 'customer_group_id', 'product_id', 'currency', 'sort_order')
);

/**
 * Catalog Rule Product Price
 */
$connection->addColumn($catalogRuleProductPriceTable, 'currency', 'varchar(3) null default null after `product_id`');
$connection->addKey(
    $catalogRuleProductPriceTable, 
    'IDX_CATALOGRULE_PRODUCT_PRICE_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($catalogRuleProductPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `website_id`');
$connection->addKey(
    $catalogRuleProductPriceTable, 
    'IDX_CATALOGRULE_PRODUCT_PRICE_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOGRULE_PRODUCT_PRICE_STORE_ID', 
    $catalogRuleProductPriceTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$databaseHelper->replaceUniqueKey(
    $installer, 
    $catalogRuleProductPriceTableName, 
    'rule_date', 
    array('rule_date', 'website_id', 'store_id', 'customer_group_id', 'product_id', 'currency')
);      

/**
 * Product Index Price
 */
$connection->addColumn($productIndexPriceTable, 'currency', 'varchar(3) null default null');
$connection->addKey(
    $productIndexPriceTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($productIndexPriceTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_INDEX_PRICE_STORE_ID', 
    $productIndexPriceTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$connection->addKey(
    $productIndexPriceTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Index
 */
$connection->addColumn($productIndexPriceIdxTable, 'currency', 'varchar(3) null default null');
$connection->addKey(
    $productIndexPriceIdxTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_IDX_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($productIndexPriceIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceIdxTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_IDX_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_INDEX_PRICE_IDX_STORE_ID', 
    $productIndexPriceIdxTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$connection->addKey(
    $productIndexPriceIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Temp
 */
$connection->addColumn($productIndexPriceTmpTable, 'currency', 'varchar(3) null default null');
$connection->addKey(
    $productIndexPriceTmpTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_TMP_CURRENCY', 
    array('currency'), 
    'index'
);
$connection->addColumn($productIndexPriceTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceTmpTable, 
    'IDX_CATALOG_PRODUCT_INDEX_PRICE_TMP_STORE_ID', 
    array('store_id'), 
    'index'
);
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_INDEX_PRICE_TMP_STORE_ID', 
    $productIndexPriceTmpTable, 
    'store_id', 
    $storeTable, 
    'store_id'
);
$connection->addKey(
    $productIndexPriceTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Final Index
 */
$connection->addColumn($productIndexPriceFinalIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceFinalIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceFinalIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Final Temp
 */
$connection->addColumn($productIndexPriceFinalTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceFinalTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceFinalTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Index
 */
$connection->addColumn($productIndexPriceBundleIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Temp
 */
$connection->addColumn($productIndexPriceBundleTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Selection Index
 */
$connection->addColumn($productIndexPriceBundleSelectionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleSelectionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleSelectionIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'selection_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Selection Temp
 */
$connection->addColumn($productIndexPriceBundleSelectionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleSelectionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleSelectionTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'selection_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Option Index
 */
$connection->addColumn($productIndexPriceBundleOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleOptionIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Bundle Option Temp
 */
$connection->addColumn($productIndexPriceBundleOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceBundleOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceBundleOptionTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Option Aggregate Index
 */
$connection->addColumn($productIndexPriceOptionAggregateIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionAggregateIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceOptionAggregateIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Option Aggregate Temp
 */
$connection->addColumn($productIndexPriceOptionAggregateTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionAggregateTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceOptionAggregateTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Option Index
 */
$connection->addColumn($productIndexPriceOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceOptionIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Option Temp
 */
$connection->addColumn($productIndexPriceOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceOptionTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Downloadable Index
 */
$connection->addColumn($productIndexPriceDownloadableIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceDownloadableIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceDownloadableIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Downloadable Temp
 */
$connection->addColumn($productIndexPriceDownloadableTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceDownloadableTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceDownloadableTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Configurable Option Aggregate Index
 */
$connection->addColumn($productIndexPriceCfgOptionAggregateIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionAggregateIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceCfgOptionAggregateIdxTable, 
    'PRIMARY', 
    array('parent_id', 'child_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Configurable Option Aggregate Temp
 */
$connection->addColumn($productIndexPriceCfgOptionAggregateTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionAggregateTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceCfgOptionAggregateTmpTable, 
    'PRIMARY', 
    array('parent_id', 'child_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Configurable Option Index
 */
$connection->addColumn($productIndexPriceCfgOptionIdxTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionIdxTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceCfgOptionIdxTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * Product Index Price Configurable Option Temp
 */
$connection->addColumn($productIndexPriceCfgOptionTmpTable, 'currency', 'varchar(3) null default null');
$connection->addColumn($productIndexPriceCfgOptionTmpTable, 'store_id', 'smallint(5) unsigned not null default 0');
$connection->addKey(
    $productIndexPriceCfgOptionTmpTable, 
    'PRIMARY', 
    array('entity_id', 'customer_group_id', 'website_id', 'currency', 'store_id'), 
    'primary'
);

/**
 * EAV Attribute
 */
$installer->run("UPDATE `{$eavAttributeTable}` 
SET `backend_model` = 'catalog/product_attribute_backend_finishdate' 
WHERE (`attribute_code` = 'special_to_date') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `{$eavEntityTypeTable}` WHERE `entity_type_code` = 'catalog_product'
))");

$installer->endSetup();
