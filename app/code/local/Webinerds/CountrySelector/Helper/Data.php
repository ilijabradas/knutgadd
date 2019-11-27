<?php

class Webinerds_CountrySelector_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Returns path to country flag
     *
     * @param string $name
     * @return string
     */
    public function getFlagPath($name = null)
    {
        $flagName = strtolower($name) . '.png';
        $filePath = Mage::getSingleton('core/design_package')->getSkinBaseUrl(array('_area' => 'adminhtml')) . DS . 'images' . DS . 'flags' . DS . $flagName;

        if (!file_exists($filePath)) {
            return Mage::getDesign()->getSkinUrl('images' . DS . 'flags' . DS . $flagName);
        } else {
            return $filePath;
        }
    }

    /**
     * Check whether country code is valid
     *
     * @param $code
     * @return bool
     */
    public function checkCountryCode($code)
    {
        $allCountries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(true);
        $code = $this->prepareCode($code);

        $isValid = false;
        foreach ($allCountries as $country) {
            if ($country['value'] == $code) {
                $isValid = true;
                break;
            }
        }

        return $isValid;
    }

    /**
     * returns countries, which can be switched on the frontend
     *
     * @return array
     */
    public function getSwitchableCountries()
    {
        $result = array();
        $allCountries = Mage::getResourceModel('directory/country_collection')->loadByStore()->toOptionArray(true);

        foreach ($allCountries as $country) {
                $result[] = $country;
        }

        return $result;
    }

    /**
     * returns Africa countries
     *
     * @return array
     */
    public function getAfricaCountries()
    {
        $result = array();
        $countryCodes = array("DZ","AO","BJ","BW","BF","BI","CM","CV","CF","TD","KM","CG","CD","CI","DJ","EG","GQ","ER","ET","GA","GM","GH","GN","GW","KE","LS","LR","LY","MG","MW","ML","MR","MU","YT","MA","MZ","NA","NE","NG","RW","RE","SH","SN","SC","SL","SO","ZA","SD","SZ","ST","TZ","TG","TN","UG","EH","ZM","ZW");
        $allCountries = $this->getSwitchableCountries();

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $countryCodes)) {
                $result[] = $country;
            }

        }

        return $result;
    }

    /**
     * returns Europe countries
     *
     * @return array
     */
    public function getEuropeCountries()
    {
        $result = array();
        $countryCodes = array('AL', 'AD', 'AT', 'BY', 'BE', 'BA', 'BG', 'HR', 'CY', 'CZ', 'DK', 'DD', 'EE', 'FO', 'FI', 'FR', 'DE', 'GI', 'GR', 'GG', 'HU', 'IS', 'IE', 'IM', 'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MK', 'MT', 'FX', 'MD', 'MC', 'ME', 'NL', 'NO', 'PL', 'PT', 'RO', 'RU', 'SM', 'RS', 'CS', 'SK', 'SI', 'ES', 'SJ', 'SE', 'CH', 'UA', 'SU', 'GB', 'VA', 'AX');
        $allCountries = $this->getSwitchableCountries();

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $countryCodes)) {
                $result[] = $country;
            }

        }

        return $result;
    }

    /**
     * returns Asia+Pacific countries
     *
     * @return array
     */
    public function getAsiaPacificCountries()
    {
        $result = array();
        $countryCodes = array('AF', 'AM', 'AZ', 'BH', 'BD', 'BT', 'BN', 'KH', 'CN', 'CY', 'GE', 'HK', 'IN', 'ID', 'IR', 'IQ', 'IL', 'JP', 'JO', 'KZ', 'KW', 'KG', 'LA', 'LB', 'MO', 'MY', 'MV', 'MN', 'MM', 'NP', 'NT', 'KP', 'OM', 'PK', 'PS', 'YD', 'PH', 'QA', 'SA', 'SG', 'KR', 'LK', 'SY', 'TW', 'TJ', 'TH', 'TL', 'TR', '™', 'AE', 'UZ', 'VN', 'YE','AS', 'AQ', 'AU', 'BV', 'IO', 'CX', 'CC', 'CK', 'FJ', 'PF', 'TF', 'GU', 'HM', 'KI', 'MH', 'FM', 'NR', 'NC', 'NZ', 'NU', 'NF', 'MP', 'PW', 'PG', 'PN', 'WS', 'SB', 'GS', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF');
        $allCountries = $this->getSwitchableCountries();

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $countryCodes)) {
                $result[] = $country;
            }

        }

        return $result;
    }

    /**
     * returns North America countries
     *
     * @return array
     */
    public function getNorthAmericaCountries()
    {
        $result = array();
        $countryCodes = array('US','CA','AI', 'AG', 'AW', 'BS', 'BB', 'BZ', 'BM', 'VG', 'KY', 'CR', 'CU', 'DM', 'DO', 'SV', 'GL', 'GD', 'GP', 'GT', 'HT', 'HN', 'JM', 'MQ', 'MX', 'MS', 'AN', 'NI', 'PA', 'PR', 'BL', 'KN', 'LC', 'MF', 'PM', 'VC', 'TT', 'TC', 'VI');
        $allCountries = $this->getSwitchableCountries();

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $countryCodes)) {
                $result[] = $country;
            }

        }

        return $result;
    }

    /**
     * returns South America countries
     *
     * @return array
     */
    public function getSouthAmericaCountries()
    {
        $result = array();
        $countryCodes = array('AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PY', 'PE', 'SR', 'UY', 'VE');
        $allCountries = $this->getSwitchableCountries();

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $countryCodes)) {
                $result[] = $country;
            }

        }

        return $result;
    }

    /**
     * Returns url, which will emulate site view from custom country
     *
     * @param $countryCode
     * @return mixed|string
     */
    public function getCountryUrl($countryCode)
    {
        $asia = array("hk", "tw");
        $lowerCase = strtolower($countryCode);
        $store = Mage::getModel('core/store')->load($lowerCase);
        $base_url = Mage::app()->getStore('default')->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        if ($store->getId()) {
            //the store exists
            return $base_url.$lowerCase;
        } elseif(in_array($lowerCase, $asia)) {
            //return Asia store
            return $base_url.'asia/';
        } else {
            //the store does not exist
            return $base_url;
        }

    }
}