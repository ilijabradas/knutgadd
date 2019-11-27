<?php
/**
 * Klarna Checkout extension
 *
 * @category    NWT
 * @package     NWT_KCO
 * @copyright   Copyright (c) 2015 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     http://nordicwebteam.se/licenses/nwtcl/1.0.txt  NWT Commercial License (NWTCL 1.0)
 * 
 *
 */

/**
 * Klarna Locale model
 * TODO to refactoring this
 */
class NWT_KCO_Model_Klarna_Locale
{

    protected $_locale = array(
        'SE'=>array(
            'name'=>'Sverige',
            'currency'=>'SEK',
            'locale'=>'sv-se',
            'test'=>array(
                'email'=>'checkout-se@testdrive.klarna.com',
                'postal_code'=>'12345',
            )
        ),
        'FI'=>array(
            'name'=>'Suomi',
            'currency'=>'EUR',
            'locale'=>'fi-fi',
            'test'=>array(
                'email'=>'checkout-fi@testdrive.klarna.com',
                'postal_code'=>'190122-829F'
            )
        ),
        'NO'=>array(
            'name'=>'Norge',
            'currency'=>'NOK',
            'locale'=>'nb-no',
            'test'=>array(
                'email'=>'checkout-no@testdrive.klarna.com',
                'postal_code'=>'0563'
            )
        ),
        'DE'=>array(
            'name'=>'Deutschland',
            'currency'=>'EUR',
            'locale'=>'de-de',
            'full-address'=>true,
            'split-street'=>true,
            'test'=>array(
                'email'=>'checkout-de@testdrive.klarna.com',
                'postal_code'=>'41460',
                'city'=>'Neuss',
                'street_name'=>'Hellersbergstr.',
                'street_number'=>'14',
                'title'=>'Herr',
                'given_name'=>'Testperson-de',
                'family_name'=>'Approved',
                'phone'=>'01522113356',
            )
        )
    );


    public function getCountry($country) {

        $country = trim(strtoupper($country));
        if(!isset($this->_locale[$country])) {
            return false;
        }
        $return = $this->_locale[$country];
        $return['country'] = $country;
        return $return;
    }

    public function getCountries() {
        return array_keys($this->_locale);
    }

    public function getLocales() {
        return $this->_locale;
    }

    public function getCurrencies($countries = array()) {
        if(!is_array($countries)) {
            $countries = array($countries);
        }
        if(!$countries) {
            $countries = $this->getCountries();
        }
        $currencies = array();
        foreach($countries as $country) {
            if(!empty($this->_locale[$country]['currency'])) {
                $currencies[] = $this->_locale[$country]['currency'];
            }
        }
        return array_unique($currencies);
    }


    public function toOptionArray($isMultiselect=false)
    {


        $return = array();

        if(!$isMultiselect) {
            $return[] = array('value'=>'', 'label'=> '');
        }

        foreach($this->_locale as $key=>$locale) {
            $return[] = array(
                'value'=>$key,
                'label'=>$locale['name']
            );
        }
        return $return;
    }


}


