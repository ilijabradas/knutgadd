<?php
/**
 * Ilija Bradas
 *
 * @package     Nebojsa_Billingcurrency
 * @author      Ilija Bradas
 * @copyright   Copyright (c) 2017 Ilija Bradas
 */

class Nebojsa_Billingcurrency_Helper_Data extends Mage_Core_Helper_Abstract {
	const DEFAULT_STORE_CURRENCY = 'USD';

	protected $_mappingCountryToCurrency = array(
		'AC' => 'USD',
		'AD' => 'EUR',
		'AE' => 'AED',
		'AF' => 'AFN',
		'AG' => 'XCD',
		'AI' => NULL,
		'AL' => 'ALL',
		'AM' => 'AMD',
		'AN' => NULL,
		'AO' => 'AOA',
		'AQ' => NULL,
		'AR' => 'ARS',
		'AS' => 'USD',
		'AT' => 'EUR',
		'AU' => 'AUD',
		'AW' => 'AWG',
		'AX' => 'EUR',
		'AZ' => 'AZN',
		'BA' => 'BAM',
		'BB' => 'BBD',
		'BD' => 'BDT',
		'BE' => 'EUR',
		'BF' => 'XOF',
		'BG' => 'BGN',
		'BH' => 'BHD',
		'BI' => 'BIF',
		'BJ' => 'XOF',
		'BL' => 'EUR',
		'BM' => 'BMD',
		'BN' => 'BND',
		'BO' => 'BOB',
		'BQ' => NULL,
		'BR' => 'BRL',
		'BS' => 'BSD',
		'BT' => 'INR',
		'BU' => NULL,
		'BV' => 'NOK',
		'BW' => 'BWP',
		'BY' => NULL,
		'BZ' => 'BZD',
		'CA' => 'CAD',
		'CC' => 'AUD',
		'CD' => 'CDF',
		'CF' => 'XAF',
		'CG' => 'XAF',
		'CH' => 'CHF',
		'CI' => 'XOF',
		'CK' => 'NZD',
		'CL' => 'CLP',
		'CM' => 'XAF',
		'CN' => 'CNY',
		'CO' => 'COP',
		'CP' => 'EUR',
		'CR' => 'CRC',
		'CS' => NULL,
		'CT' => NULL,
		'CU' => 'CUP',
		'CV' => 'CVE',
		'CW' => 'ANG',
		'CX' => 'AUD',
		'CY' => 'EUR',
		'CZ' => 'CZK',
		'DD' => NULL,
		'DE' => 'EUR',
		'DG' => 'USD',
		'DJ' => 'DJF',
		'DK' => 'DKK',
		'DM' => 'XCD',
		'DO' => 'DOP',
		'DY' => NULL,
		'DZ' => 'DZD',
		'EA' => 'EUR',
		'EC' => 'USD',
		'EE' => 'EUR',
		'EG' => 'EGP',
		'EH' => 'MAD',
		'ER' => 'ERN',
		'ES' => 'EUR',
		'ET' => 'ETB',
		'EU' => 'EUR',
		'FI' => 'EUR',
		'FJ' => 'FJD',
		'FK' => 'FKP',
		'FM' => 'USD',
		'FO' => 'DKK',
		'FQ' => NULL,
		'FR' => 'EUR',
		'FX' => 'EUR',
		'GA' => 'XAF',
		'GB' => 'GBP',
		'GD' => 'XCD',
		'GE' => NULL,
		'GF' => 'EUR',
		'GG' => 'GBP',
		'GH' => 'GHS',
		'GI' => 'GIP',
		'GL' => 'DKK',
		'GM' => 'GMD',
		'GN' => 'GNF',
		'GP' => 'EUR',
		'GQ' => 'XAF',
		'GR' => 'EUR',
		'GS' => 'GBP',
		'GT' => 'GTQ',
		'GU' => 'USD',
		'GW' => 'XOF',
		'GY' => 'GYD',
		'HK' => 'HKD',
		'HM' => 'AUD',
		'HN' => 'HNL',
		'HR' => 'HRK',
		'HT' => 'HTG',
		'HU' => 'HUF',
		'HV' => NULL,
		'IC' => 'EUR',
		'ID' => 'IDR',
		'IE' => 'EUR',
		'IL' => 'ILS',
		'IM' => 'GBP',
		'IN' => 'INR',
		'IO' => 'USD',
		'IQ' => 'IQD',
		'IR' => 'IRR',
		'IS' => 'ISK',
		'IT' => 'EUR',
		'JE' => 'GBP',
		'JM' => 'JMD',
		'JO' => 'JOD',
		'JP' => 'JPY',
		'JT' => NULL,
		'KE' => 'KES',
		'KG' => 'KGS',
		'KH' => 'KHR',
		'KI' => 'AUD',
		'KM' => 'KMF',
		'KN' => 'XCD',
		'KP' => 'KPW',
		'KR' => 'KRW',
		'KW' => 'KWD',
		'KY' => 'KYD',
		'KZ' => 'KZT',
		'LA' => 'LAK',
		'LB' => 'LBP',
		'LC' => 'XCD',
		'LI' => 'CHF',
		'LK' => 'LKR',
		'LR' => 'LRD',
		'LS' => 'LSL',
		'LT' => 'EUR',
		'LU' => 'EUR',
		'LV' => 'EUR',
		'LY' => 'LYD',
		'MA' => 'MAD',
		'MC' => 'EUR',
		'MD' => 'MDL',
		'ME' => 'EUR',
		'MF' => 'EUR',
		'MG' => 'MGA',
		'MH' => 'USD',
		'MI' => NULL,
		'MK' => 'MKD',
		'ML' => 'XOF',
		'MM' => 'MMK',
		'MN' => 'MNT',
		'MO' => 'MOP',
		'MP' => 'USD',
		'MQ' => 'EUR',
		'MR' => 'MRO',
		'MS' => 'XCD',
		'MT' => 'EUR',
		'MU' => 'MUR',
		'MV' => 'MVR',
		'MW' => 'MWK',
		'MX' => 'MXN',
		'MY' => 'MYR',
		'MZ' => 'MZN',
		'NA' => 'NAD',
		'NC' => 'XPF',
		'NE' => 'XOF',
		'NF' => 'AUD',
		'NG' => 'NGN',
		'NH' => NULL,
		'NI' => 'NIO',
		'NL' => 'EUR',
		'NO' => 'NOK',
		'NP' => 'NPR',
		'NQ' => NULL,
		'NR' => 'AUD',
		'NT' => NULL,
		'NU' => 'NZD',
		'NZ' => 'NZD',
		'OM' => 'OMR',
		'PA' => 'PAB',
		'PC' => NULL,
		'PE' => 'PEN',
		'PF' => 'XPF',
		'PG' => 'PGK',
		'PH' => 'PHP',
		'PK' => 'PKR',
		'PL' => 'PLN',
		'PM' => 'EUR',
		'PN' => 'NZD',
		'PR' => 'USD',
		'PS' => 'JOD',
		'PT' => 'EUR',
		'PU' => NULL,
		'PW' => 'USD',
		'PY' => 'PYG',
		'PZ' => NULL,
		'QA' => 'QAR',
		'RE' => 'EUR',
		'RH' => NULL,
		'RO' => 'RON',
		'RS' => 'RSD',
		'RU' => 'RUB',
		'RW' => 'RWF',
		'SA' => 'SAR',
		'SB' => 'SBD',
		'SC' => 'SCR',
		'SD' => 'SDG',
		'SE' => 'SEK',
		'SG' => 'SGD',
		'SH' => 'SHP',
		'SI' => 'EUR',
		'SJ' => 'NOK',
		'SK' => NULL,
		'SL' => 'SLL',
		'SM' => 'EUR',
		'SN' => 'XOF',
		'SO' => 'SOS',
		'SR' => 'SRD',
		'SS' => 'SSP',
		'ST' => 'STD',
		'SU' => 'RUB',
		'SV' => 'USD',
		'SX' => 'ANG',
		'SY' => 'SYP',
		'SZ' => 'SZL',
		'TA' => 'GBP',
		'TC' => 'USD',
		'TD' => 'XAF',
		'TF' => 'EUR',
		'TG' => 'XOF',
		'TH' => 'THB',
		'TJ' => 'TJS',
		'TK' => 'NZD',
		'TL' => 'USD',
		'TM' => 'TMT',
		'TN' => 'TND',
		'TO' => 'TOP',
		'TP' => NULL,
		'TR' => 'TRY',
		'TT' => 'TTD',
		'TV' => 'AUD',
		'TW' => 'TWD',
		'TZ' => 'TZS',
		'UA' => 'UAH',
		'UG' => 'UGX',
		'UK' => 'GBP',
		'UM' => 'USD',
		'US' => 'USD',
		'UY' => 'UYU',
		'UZ' => 'UZS',
		'VA' => 'EUR',
		'VC' => 'XCD',
		'VD' => NULL,
		'VE' => 'VEF',
		'VG' => 'USD',
		'VI' => 'USD',
		'VN' => 'VND',
		'VU' => 'VUV',
		'WF' => 'XPF',
		'WK' => NULL,
		'WS' => 'WST',
		'XK' => 'EUR',
		'YD' => NULL,
		'YE' => 'YER',
		'YT' => 'EUR',
		'YU' => NULL,
		'ZA' => 'ZAR',
		'ZM' => 'ZMW',
		'ZR' => NULL,
		'ZW' => 'USD',
	);
	/**
	 * get store view code by country
	 * @param $country
	 * @return bool
	 */
	public function getStoreCodeByCountry($country) {
		if (isset($this->_mappingCountryToCurrency[$country])) {
			return $this->_mappingCountryToCurrency[$country];
		}
		return self::DEFAULT_STORE_CURRENCY;
	}
}