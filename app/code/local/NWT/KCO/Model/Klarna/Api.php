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
 * Load Klarna lib files
 */
if(!class_exists('Klarna',$autoload=false)) {
    require_once(dirname(__FILE__).'/lib/Api/Klarna.php');
    require_once(dirname(__FILE__).'/lib/Api/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
    require_once(dirname(__FILE__).'/lib/Api/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
}


/**
 * Klarna API Model 
 */
class NWT_KCO_Model_Klarna_Api extends Klarna
{


    protected $_helper;

    public function __construct() {
        $this->_helper = Mage::helper('nwtkco');
        return parent::__construct();
    }


    public function initFromCountry($countryCode,$test,$store = null) {

        $country     = KlarnaCountry::fromCode($countryCode);

        if(!$country) {
            Mage::throwException($this->_helper->__('Country not allowed (%s).',$countryCode));
        }
        $currency           = KlarnaCountry::getCurrency($country);
        $language           = KlarnaCountry::getLanguage($country);


        $this->config(
                $this->_helper->getEid($store),
                $this->_helper->getSharedSecret($store),
                $country,
                $language,
                $currency,
                $test?Klarna::BETA:Klarna::LIVE,
                'json',
                './pclasses.json'
        );
        if(!$this->getCountry()) {
            //country, language, currency will be set null (instead of throwException) if is wrong, in Klarna::init
            Mage::throwException($this->_helper->__("Country not allowed (%s).",$countryCode));
        }
        return $this;
    }


    public function initFromOrder($order)
    {

       $payment = $order->getPayment();
       if(!$payment->getKlarnaReservation()) {
           Mage::throwException($this->_helper->__("Invalid payment, missing reservation."));
       }


       $order   = $payment->getOrder();
       $test    = $payment->getKlarnaTest();


        $countryCode = $order->getBillingAddress()->getCountryId();
        $country     = KlarnaCountry::fromCode($countryCode);

        if(!$country) {
            Mage::throwException($helper->__('Country not allowed (%s).',$countryCode));
        }

        $currency           = KlarnaCountry::getCurrency($country);
        $currencyCode       = KlarnaCurrency::getCode($currency);
        $orderCurrencyCode  = $order->getOrderCurrencyCode();


        if(trim(strtoupper($currencyCode)) != trim(strtoupper($orderCurrencyCode))) {
            Mage::throwException($helper->__('Currency not allowed (%s, expecting %s)',$orderCurrencyCode,$currencyCode));
        }


        $language = KlarnaCountry::getLanguage($country);
        $languageCode = KlarnaLanguage::fromCode($language);

        $store = $order->getStore();


        $this->config(
                $this->_helper->getEid($store),
                $this->_helper->getSharedSecret($store),
                $country,
                $language,
                $currency,
                $test?Klarna::BETA:Klarna::LIVE,
                'json',
                './pclasses.json'
        );
        if(!$this->getCountry()) {
            //country, language, currency will be set null (instead of throwException) if is wrong, in Klarna::init
            Mage::throwException($helper->__("Country / Currency / Language not allowed %s/%s/%s",$countryCode,$orderCurrencyCode,$languageCode));
        }
        return $this;
    }


}
