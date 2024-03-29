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

require_once rtrim(dirname(__FILE__), '/').'/../../../../../Core/Catalog/Product/Price/Price/Import.php';

/**
 * Product price import
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_AdvancedPricing_Catalog_Product_Price_Price_Import 
    extends Innoexts_Shell_Core_Catalog_Product_Price_Price_Import 
{
    
}

$shell = new Innoexts_Shell_AdvancedPricing_Catalog_Product_Price_Price_Import();
$shell->run();

/**
 * 

php shell/Innoexts/AdvancedPricing/Catalog/Product/Price/Price/Import.php \
    --file-path /var/import/ \
    --file-filename localfilename.csv

php shell/Innoexts/AdvancedPricing/Catalog/Product/Price/Price/Import.php \
    --ftp \
    --ftp-host ftp.yourhost.com \
    --ftp-user username \
    --ftp-password password \
    --ftp-filename remotefilename.csv \
    --file-path /var/import/ \
    --file-filename localfilename.csv

php shell/Innoexts/AdvancedPricing/Catalog/Product/Price/Price/Import.php \
    --file-path /var/import/Innoexts/AdvancedPricing/ \
    --file-filename product-price.csv

 **/

