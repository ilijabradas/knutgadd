<?php
/**
 * Atwix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Atwix Mod
 * @package     Atwix_Ipstoreswitcher
 * @author      Atwix Core Team
 * @copyright   Copyright (c) 2014 Atwix (http://www.atwix.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Atwix_Ipstoreswitcher_Helper_Data extends Mage_Core_Helper_Abstract {
	const DEFAULT_STORE_CODE = 'default';

	/**
	 * countries to store relation
	 * default is default
	 * @var array
	 */
	protected $_countryToStoreCode = array(
		// 'AD' => 'eu',
		// 'AL' => 'eu',
		// 'AT' => 'eu',
		// 'BA' => 'eu',
		// 'BE' => 'eu',
		// 'BG' => 'eu',
		// 'BY' => 'eu',
		// 'CH' => 'eu',
		// 'CY' => 'eu',
		// 'CZ' => 'eu',
		// 'DE' => 'eu',
		// 'DK' => 'eu',
		// 'EE' => 'eu',
		// 'ES' => 'eu',
		// 'FI' => 'eu',
		// 'FO' => 'eu',
		// 'FR' => 'eu',
		// 'GB' => 'eu',
		// 'GBR' => 'eu',
		// 'UK' => 'eu',
		// 'GI' => 'eu',
		// 'GR' => 'eu',
		// 'HR' => 'eu',
		// 'HU' => 'eu',
		// 'IE' => 'eu',
		// 'IM' => 'eu',
		// 'IS' => 'eu',
		// 'IT' => 'eu',
		// 'LI' => 'eu',
		// 'LT' => 'eu',
		// 'LU' => 'eu',
		// 'LV' => 'eu',
		// 'MC' => 'eu',
		// 'MD' => 'eu',
		// 'ME' => 'eu',
		// 'MK' => 'eu',
		// 'MT' => 'eu',
		// 'NL' => 'eu',
		// 'NO' => 'eu',
		// 'PL' => 'eu',
		// 'PT' => 'eu',
		// 'RO' => 'eu',
		// 'RS' => 'se',
		// 'RU' => 'eu',
		// // 'SE' => 'se',
		// 'SI' => 'eu',
		// 'SK' => 'eu',
		// 'SM' => 'eu',
		// 'UA' => 'eu',
		// 'VA' => 'eu',
		// // 'JP' => 'jp',
		// 'KR' => 'kr',
		// 'HK' => 'cn',
		// 'CN' => 'cn',
		// 'CHN' => 'cn',
		// 'ZH' => 'cn',
		// 'TW' => 'tw',
	);

	/**
	 * get store view code by country
	 * @param $country
	 * @return bool
	 */
	public function getStoreCodeByCountry($country) {
		if (isset($this->_countryToStoreCode[$country])) {
			return $this->_countryToStoreCode[$country];
		}
		return self::DEFAULT_STORE_CODE;
	}
}
