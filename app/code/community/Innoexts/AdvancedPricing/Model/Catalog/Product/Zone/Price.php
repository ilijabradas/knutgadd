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

/**
 * Product zone price
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Catalog_Product_Zone_Price 
    extends Innoexts_Core_Model_Area_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('catalog/product_zone_price');
    }
    /**
     * Get advanced pricing helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Data
     */
    public function getAdvancedPricingHelper()
    {
        return Mage::helper('advancedpricing');
    }
    /**
     * Get filters
     * 
     * @return array
     */
    protected function getFilters()
    {
        $filters = array(
            'country_id'     => $this->getCountryFilter(), 
            'region_id'      => $this->getRegionFilter('country_id'), 
            'is_zip_range'   => $this->getTextFilter(), 
            'zip'            => $this->getZipFilter(), 
            'from_zip'       => $this->getTextFilter(), 
            'to_zip'         => $this->getTextFilter(), 
            'price'          => $this->getTextFilter(), 
            'price_type'     => $this->getTextFilter(), 
        );
        return $filters;
    }
    /**
     * Validate range
     * 
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     * 
     * @return boolean
     */
    public function validatePriceType($value)
    {
        if (in_array($value, array('fixed', 'percent'))) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get price type validator
     * 
     * @return Zend_Validate
     */
    protected function getPriceTypeValidator()
    {
        $validator = $this->getTextValidator(true);
        $validator->addValidator(new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::STRING), true);
        $validator->addValidator(new Zend_Validate_Callback(array(
            'callback' => array($this, 'validatePriceType'), 
        )), true);
        return $validator;
    }
    /**
     * Get validators
     * 
     * @return array
     */
    protected function getValidators()
    {
        $helper = $this->getAdvancedPricingHelper();
        $validators = array(
            'country_id'     => $this->getTextValidator(false, 0, 4), 
            'region_id'      => $this->getIntegerValidator(false, 0), 
            'price'          => $this->getFloatValidator(true, 0), 
            'price_type'     => $this->getPriceTypeValidator(), 
            'is_zip_range'   => $this->getIntegerValidator(false, 0), 
        );
        $isZipRange = $this->getIsZipRange();
        if ($isZipRange) {
            $maxZipValue = $helper->getConfig()->getMaxZipValue();
            $fromZip = (int) $this->getFromZip();
            $validators['from_zip'] = $this->getIntegerValidator(true, 1, $maxZipValue);
            $validators['to_zip'] = $this->getIntegerValidator(true, $fromZip, $maxZipValue);
        } else {
            $validators['zip'] = $this->getTextValidator(false, 0, 10);
        }
        return $validators;
    }
    /**
     * Validate product zone price
     *
     * @throws Mage_Core_Exception
     * 
     * @return bool
     */
    public function validate()
    {
        $helper = $this->getAdvancedPricingHelper();
        if (parent::validate()) {
            $isZipRange = $this->getIsZipRange();
            if ($isZipRange) {
                $this->setZip($this->getFromZip().'-'.$this->getToZip());
            } else {
                $this->setFromZip(null);
                $this->setToZip(null);
            }
            $errorMessages = array();
            $productZonePrice = Mage::getModel('catalog/product_zone_price')->loadByRequest($this);
            if ($productZonePrice->getId()) {
                array_push($errorMessages, $helper->__('Duplicated discount.'));
            }
            if (count($errorMessages)) {
                Mage::throwException(join("\n", $errorMessages));
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * Load product zone price by product identifier and address
     * 
     * @param int $productId
     * @param Varien_Object $address
     * 
     * @return self
     */
    public function loadByProductIdAndAddress($productId, $address)
    {
        $this->_getResource()->loadByProductIdAndAddress($this, $productId, $address);
        $this->setOrigData();
        return $this;
    }
    /**
     * Load product zone price by request
     * 
     * @param Varien_Object $request
     * 
     * @return self
     */
    public function loadByRequest(Varien_Object $request)
    {
        $this->_getResource()->loadByRequest($this, $request);
        $this->setOrigData();
        return $this;
    }
    /**
     * Check if fixed price type
     * 
     * @return bool
     */
    public function isFixedPriceType()
    {
        return ($this->getPriceType() == 'fixed') ? true : false;
    }
    /**
     * Get final price
     * 
     * @param float $price
     * 
     * @return float
     */
    public function getFinalPrice($price)
    {
        if ($this->isFixedPriceType()) {
            return ($this->getPrice() < $price) ? round($price - $this->getPrice(), 4) : $price;
        } else {
            return ($this->getPrice() < 100) ? round($price -  ($price * ($this->getPrice() / 100)), 4) : $price;
        }
    }
}