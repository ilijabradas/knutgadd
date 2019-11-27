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

$installer                                  = $this;

$connection                                 = $installer->getConnection();
$helper                                     = Mage::helper('advancedpricing');
$versionHelper                              = $helper->getVersionHelper();
$databaseHelper                             = $helper->getCoreHelper()->getDatabaseHelper();

$installer->startSetup();

$productTable                               = $installer->getTable('catalog/product');
$storeTable                                 = $installer->getTable('core/store');
$customerGroupTable                         = $installer->getTable('customer/customer_group');

$productCompoundPriceTable                  = $installer->getTable('catalog/product_compound_price');
$productCompoundSpecialPriceTable           = $installer->getTable('catalog/product_compound_special_price');
$productIndexCompoundPriceTable             = $installer->getTable('catalog/product_index_compound_price');
$productIndexCompoundSpecialPriceTable      = $installer->getTable('catalog/product_index_compound_special_price');

$catalogRuleTable                           = $installer->getTable('catalogrule/rule');
$catalogRuleCompoundDiscountAmountTable     = $installer->getTable('catalogrule/compound_discount_amount');
$catalogRuleCompoundSubDiscountAmountTable  = $installer->getTable('catalogrule/compound_sub_discount_amount');

$catalogRuleGroupStoreTable                 = $installer->getTable('catalogrule/rule_group_store');

if ($versionHelper->isGe1700()) {
    $catalogRuleStoreTable                      = $installer->getTable('catalogrule/store');
    $catalogRuleCurrencyTable                   = $installer->getTable('catalogrule/currency');
}
$catalogRuleProductTableName                = 'catalogrule/rule_product';
$catalogRuleProductTable                    = $installer->getTable($catalogRuleProductTableName);
$catalogRuleProductPriceTableName           = 'catalogrule/rule_product_price';
$catalogRuleProductPriceTable               = $installer->getTable($catalogRuleProductPriceTableName);

/* Product Compound Price */
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
    
/* Product Compound Special Price */
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

/* Product Index Compound Price */
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

/* Product Index Compound Special Price */
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

/* Catalog Rule */
if (!$versionHelper->isGe1700()) {
    $connection->addColumn($catalogRuleTable, 'store_ids', 'text null default null after `website_ids`');
    $connection->addColumn($catalogRuleTable, 'currencies', 'text null default null after `store_ids`');
}
    
/* Catalog Rule Compound Discount Amount */
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
    
/* Catalog Rule Compound Sub Discount Amount */
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
$connection->addKey($catalogRuleProductTable, 'IDX_CATALOGRULE_PRODUCT_CURRENCY', array('currency'), 'index');

$connection->addColumn($catalogRuleProductTable, 'store_id', 'smallint(5) unsigned not null default 0 after `website_id`');
$connection->addKey($catalogRuleProductTable, 'IDX_CATALOGRULE_PRODUCT_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOGRULE_PRODUCT_STORE_ID', $catalogRuleProductTable, 'store_id', $storeTable, 'store_id');

$databaseHelper->replaceUniqueKey(
    $installer, $catalogRuleProductTableName, 'sort_order', array(
        'rule_id', 'from_time', 'to_time', 'website_id', 'store_id', 'customer_group_id', 'product_id', 'currency', 'sort_order'
    )
);

/**
 * Catalog Rule Product Price
 */
$connection->addColumn($catalogRuleProductPriceTable, 'currency', 'varchar(3) null default null after `product_id`');
$connection->addKey($catalogRuleProductPriceTable, 'IDX_CATALOGRULE_PRODUCT_PRICE_CURRENCY', array('currency'), 'index');

$connection->addColumn($catalogRuleProductPriceTable, 'store_id', 'smallint(5) unsigned not null default 0 after `website_id`');
$connection->addKey($catalogRuleProductPriceTable, 'IDX_CATALOGRULE_PRODUCT_PRICE_STORE_ID', array('store_id'), 'index');
$connection->addConstraint('FK_CATALOGRULE_PRODUCT_PRICE_STORE_ID', $catalogRuleProductPriceTable, 'store_id', $storeTable, 'store_id');

$databaseHelper->replaceUniqueKey(
    $installer, $catalogRuleProductPriceTableName, 'rule_date', array(
        'rule_date', 'website_id', 'store_id', 'customer_group_id', 'product_id', 'currency'
    )
);

$installer->endSetup();