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
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../../../../Core/Catalog/Product/Price/Group/Export.php';

/**
 * Product group price export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_AdvancedPricing_Catalog_Product_Price_Group_Export 
    extends Innoexts_Shell_Core_Catalog_Product_Price_Group_Export 
{
    /**
     * Get advanced pricing helper
     *
     * @return Innoexts_AdvancedPricing_Helper_Data
     */
    protected function getAdvancedPricingHelper()
    {
        return Mage::helper('advancedpricing');
    }
    /**
     * Get datum additional conditions array
     * 
     * @param array $data
     * 
     * @return array
     */
    protected function getDatumAdditionalConditionsArray($datum)
    {
        $adapter = $this->getWriteAdapter();
        return array(
            "(currency              = {$adapter->quote($datum['currency'])})", 
            "(store_id              = {$adapter->quote($datum['store_id'])})", 
        );
    }
    /**
     * Get additional field names
     * 
     * @return array
     */
    protected function getAdditionalFieldNames()
    {
        return array(
            'currency', 
            'store', 
        );
    }
    /**
     * Get row additional fields
     * 
     * @param array $item
     * 
     * @return array
     */
    protected function getRowAdditionalFields($item)
    {
        return array(
            'currency'          => $item['currency'], 
            'store'             => ($item['store_id']) ? $this->getCoreHelper()
                ->getStoreCodeById($item['store_id']) : '', 
        );
    }
}

$shell = new Innoexts_Shell_AdvancedPricing_Catalog_Product_Price_Group_Export();
$shell->run();

/**
 * 

php shell/Innoexts/AdvancedPricing/Catalog/Product/Price/Group/Export.php \
    --file-path /var/export/ \
    --file-filename localfilename.csv

php shell/Innoexts/AdvancedPricing/Catalog/Product/Price/Group/Export.php \
    --file-path /var/export/Innoexts/AdvancedPricing/ \
    --file-filename product-group-price.csv

 **/