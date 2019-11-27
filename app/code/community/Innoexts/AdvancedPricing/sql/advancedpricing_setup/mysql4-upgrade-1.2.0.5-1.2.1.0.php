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

$storeTable                                     = $installer->getTable('core/store');

$productGroupPriceTableName                     = 'catalog/product_attribute_group_price';
$productGroupPriceTable                         = $installer->getTable($productGroupPriceTableName);

$productIndexGroupPriceTableName                = 'catalog/product_index_group_price';
$productIndexGroupPriceTable                    = $installer->getTable($productIndexGroupPriceTableName);

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

$installer->endSetup();