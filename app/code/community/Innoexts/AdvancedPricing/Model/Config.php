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
 * Advanced pricing config
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Config 
    extends Varien_Object 
{
    /**
     * Max zip code length 
     */
    const MAX_ZIP_LENGTH                             = 10;
    /**
     * Max zip value
     */
    const MAX_ZIP_VALUE                              = 9999999999;
    /**
     * Get max zip length 
     */
    public function getMaxZipLength()
    {
        return self::MAX_ZIP_LENGTH;
    }
    /**
     * Get max zip value 
     */
    public function getMaxZipValue()
    {
        return self::MAX_ZIP_VALUE;
    }
}