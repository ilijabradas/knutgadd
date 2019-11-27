UPDATE `[prefix]_eav_attribute` SET `backend_model` = 'eav/entity_attribute_backend_datetime'
WHERE (`attribute_code` = 'special_to_date') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `[prefix]_eav_entity_type` WHERE `entity_type_code` = 'catalog_product'
));

UPDATE `[prefix]_catalog_eav_attribute` SET `is_global` = 1 WHERE `attribute_id` IN (
    SELECT `attribute_id` FROM `[prefix]_eav_attribute` WHERE (`attribute_code` IN (
        'price', 'special_price', 'special_from_date', 'special_to_date', 'tier_price'
    )) AND (`entity_type_id` = (
        SELECT `entity_type_id` FROM `[prefix]_eav_entity_type` WHERE `entity_type_code` = 'catalog_product')
    )
);

UPDATE `[prefix]_core_config_data` SET `value` = '0' WHERE `path`  = 'catalog/price/scope';

TRUNCATE TABLE `[prefix]_catalog_product_index_price`;
ALTER TABLE `[prefix]_catalog_product_index_price` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price` DROP FOREIGN KEY FK_CATALOG_PRODUCT_INDEX_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_index_price` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_idx` DROP FOREIGN KEY FK_CATALOG_PRODUCT_INDEX_PRICE_IDX_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_index_price_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_tmp` DROP FOREIGN KEY FK_CATALOG_PRODUCT_INDEX_PRICE_TMP_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_index_price_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_final_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_final_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_final_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_final_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_final_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_final_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_sel_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_sel_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_sel_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_sel_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_sel_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_sel_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_opt_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_opt_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_opt_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_bundle_opt_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_opt_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_bundle_opt_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_opt_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_opt_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_opt_agr_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_agr_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_agr_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_opt_agr_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_agr_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_opt_agr_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_downlod_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_downlod_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_downlod_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_downlod_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_downlod_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_downlod_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_cfg_opt_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_cfg_opt_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_tmp` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_idx`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_idx` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_idx` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_tmp`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_tmp` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_price_cfg_opt_agr_tmp` DROP `store_id`;

DROP TABLE IF EXISTS `[prefix]_catalogrule_compound_discount_amount`;
DROP TABLE IF EXISTS `[prefix]_catalogrule_compound_sub_discount_amount`;
DROP TABLE IF EXISTS `[prefix]_catalogrule_group_store`;
DROP TABLE IF EXISTS `[prefix]_catalogrule_store`;
DROP TABLE IF EXISTS `[prefix]_catalogrule_currency`;

TRUNCATE TABLE `[prefix]_catalogrule_product`;
ALTER TABLE `[prefix]_catalogrule_product` DROP `currency`;
ALTER TABLE `[prefix]_catalogrule_product` DROP FOREIGN KEY FK_CATALOGRULE_PRODUCT_STORE_ID;
ALTER TABLE `[prefix]_catalogrule_product` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalogrule_product_price`;
ALTER TABLE `[prefix]_catalogrule_product_price` DROP `currency`;
ALTER TABLE `[prefix]_catalogrule_product_price` DROP FOREIGN KEY FK_CATALOGRULE_PRODUCT_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalogrule_product_price` DROP `store_id`;

DROP TABLE IF EXISTS `[prefix]_catalog_product_zone_price`;

DELETE FROM `[prefix]_catalog_product_entity_tier_price` WHERE (`currency` IS NOT NULL) OR (`store_id` <> 0);
ALTER TABLE `[prefix]_catalog_product_entity_tier_price` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_entity_tier_price` DROP FOREIGN KEY FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_entity_tier_price` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_tier_price`;
ALTER TABLE `[prefix]_catalog_product_index_tier_price` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_tier_price` DROP FOREIGN KEY FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_index_tier_price` DROP `store_id`;

DELETE FROM `[prefix]_catalog_product_entity_group_price` WHERE (`currency` IS NOT NULL) OR (`store_id` <> 0);
ALTER TABLE `[prefix]_catalog_product_entity_group_price` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_entity_group_price` DROP FOREIGN KEY FK_CATALOG_PRODUCT_ENTITY_GROUP_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_entity_group_price` DROP `store_id`;

TRUNCATE TABLE `[prefix]_catalog_product_index_group_price`;
ALTER TABLE `[prefix]_catalog_product_index_group_price` DROP `currency`;
ALTER TABLE `[prefix]_catalog_product_index_group_price` DROP FOREIGN KEY FK_CATALOG_PRODUCT_INDEX_GROUP_PRICE_STORE_ID;
ALTER TABLE `[prefix]_catalog_product_index_group_price` DROP `store_id`;

DROP TABLE IF EXISTS `[prefix]_catalog_product_compound_price`;
DROP TABLE IF EXISTS `[prefix]_catalog_product_compound_special_price`;
DROP TABLE IF EXISTS `[prefix]_catalog_product_index_compound_price`;
DROP TABLE IF EXISTS `[prefix]_catalog_product_index_compound_special_price`;

DELETE FROM `[prefix]_core_resource` WHERE `code` = 'advancedpricing_setup';
