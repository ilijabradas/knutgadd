DELETE FROM `[prefix]_catalog_product_entity_decimal` WHERE (`attribute_id` = (
    SELECT `attribute_id` FROM `[prefix]_eav_attribute` WHERE `attribute_code` = 'special_price'
)) AND (`value` = 0);