DELETE FROM `catalog_product_entity_decimal` WHERE (`attribute_id` = (
    SELECT `attribute_id` FROM `eav_attribute` WHERE `attribute_code` = 'special_price'
)) AND (`value` = 0);