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
 * Default helper
 * Used also as "config" helper
 */
class NWT_KCO_Helper_Data extends Mage_Core_Helper_Abstract
{

  
    const XML_NWTKCO_PATH = 'nwtkco/settings/';

    const COOKIE_PRODUCTS_TOOGLED     = 'NwtKCOProductsToogled';
    const COOKIE_CART_CTRL_KEY        = 'NwtKCOCartCtrlKey';


    public function getKlarnaCheckout()             { return Mage::getSingleton('nwtkco/checkout'); }

    public function getCheckoutPath($path = null) {
        return 'nwtkco/checkout/'.trim(ltrim($path,'/'));
    }

    public function getCheckoutUrl($path = null, $params = array())   { 
        return Mage::getUrl($this->getCheckoutPath($path),$params);
    }


    public function getTermsUrl($store = null) {
    
        //if there are multiple pages with same url key; magento will generate options with key|id
        //@see Mage_Cms_Model_Resource_Page_Collection::toOptionArray

        $url = explode('|',(string)Mage::getStoreConfig(self::XML_NWTKCO_PATH.'terms_url',$store));
        return $url[0];

    }
    
    
    public function getBuyTermsUri($store = null) {
        return Mage::getUrl('',array('_direct'=>(string)$this->getTermsUrl(),'_store'=>$store));
    }

    
    public function getMinimumAge($store = null) {
        if($this->isMinimumAgeRequired($store)) {
	  return (int)$this->_getMinimumAge();
	}
	return 0;
    }

    protected $_countries = null;

    public function getCountries($store = null) {

        if(is_null($this->_countries)) {

            $mageAllowCountries = preg_split("#\s*[ ,;]\s*#",strtoupper((string)Mage::getStoreConfig('general/country/allow',$store)),null, PREG_SPLIT_NO_EMPTY);
            $klrnAllowCountries = Mage::getSingleton('nwtkco/klarna_locale')->getCountries();
            $this->_countries = array_intersect($mageAllowCountries,$klrnAllowCountries);
            //KCO specific country
            if((int)$this->getAllowspecific()> 0) {
                $specificCountries =  preg_split("#\s*[ ,;]\s*#",strtoupper((string)$this->getSpecificcountry()),null,PREG_SPLIT_NO_EMPTY);
                if($specificCountries) {
                    $this->_countries = array_intersect($this->_countries,$specificCountries);
                }
            }
            //add default country first
            $defaultCountry = strtoupper(trim((string)$this->getCountry()));
            $key = array_search($defaultCountry, $this->_countries);
            if($key) { //intentionally not tested with  !== false, if is on first position do nothing
                unset($this->_countries[$key]);
                array_unshift($this->_countries,$defaultCountry); //add default on first position
            }
        }
        return $this->_countries;
    }

    public function getDefaultLocale() {
        $countries = $this->getCountries();
        if(!$countries) {
            return false;
        }
        $locale = Mage::getSingleton('nwtkco/klarna_locale')->getCountry($countries[0]);
        if(empty($locale['locale'])) {
            return false;
        }
        return $locale['locale'];
    }



    public function getCheckoutLinks($store = null) {
        $links = trim(Mage::getStoreConfig(self::XML_NWTKCO_PATH.'checkout_links',$store));
        return preg_split("#\s*[ ,;]\s*#", $links, null, PREG_SPLIT_NO_EMPTY);
    }

    public function getShowCartItemsCookieName()        { return self::COOKIE_PRODUCTS_TOOGLED; }
    public function getCartCtrlKeyCookieName()          { return self::COOKIE_CART_CTRL_KEY; }

    public function showCartItems($store = null) {
        if(!isset($_COOKIE[self::COOKIE_PRODUCTS_TOOGLED])){
            return Mage::getStoreConfigFlag(self::XML_NWTKCO_PATH.'show_cart_items',$store);
        } else {
            return !empty($_COOKIE[self::COOKIE_PRODUCTS_TOOGLED]);
        }
    }

    public function subscribeNewsletter($quote = null) {

        if(!$quote) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        if($quote->getPayment()) {
            $status = $quote->getPayment()->getAdditionalInformation("nwtkco_newsletter");
        } else {
            $status = null;
        }

        if($status) { //when is set (in quote) is -1 for NO, 1 for Yes
            return $status>0;
        } else { //get default value
            return Mage::getStoreConfigFlag(self::XML_NWTKCO_PATH.'newsletter_subscribe',$quote->getStore());
        }
    }

    public function _hexIsValid($hex){
        $hex = str_replace('#','',$hex);
        return ctype_xdigit($hex);
    }

    public function writeLog($message,$force = false) {

        if($message && ($force || Mage::getStoreConfig(self::XML_NWTKCO_PATH.'log'))) {
            Mage::log($message,null,"kco-order-create.log");
        }
        return $this;

    }
    
    
    public function addKlarnakassanLink($store = null) {
        return $this->isEnabled($store) && $this->_addKlarnakassanLink($store);
    }

    public function addKlarnakassanButton($store = null) {
        return $this->isEnabled($store) && $this->_addKlarnakassanButton($store);
    }
    
    public function removeCartCheckoutButtons($store = null) {
        return $this->addKlarnakassanButton($store) && $this->_removeCartCheckoutButtons($store);
    }

    public function removeCheckoutLinks($store = null) {
        return $this->addKlarnakassanLink($store) && $this->_removeCheckoutLinks($store);
    }


    //this is not (anymore) required, KLARNA (finally made their iframe resposnsive)
    //we required here (to return false), for older phtml files, which still used it
    public function changeLayout($store = null) {
        return false;
    }



    /**
     * Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        $store = isset($args[0]) ? $args[0] : null;
        
        if($method{0} == '_') {
            // _isEnable will be similar with isEnable, _getTitle with getTitle..., _removeCheckoutLinks with removeCheckoutLinks
            // usefull when want to override a method and use default getter
            $method = substr($method,1);
        }

        //getBuyTermsUri => return getStoreConfig('payment/nwtkco/buy_terms_uri')
        if(substr($method, 0, 3) == 'get') {
            $key = self::XML_NWTKCO_PATH.$this->_underscore(substr($method,3));
            return Mage::getStoreConfig($key,$store);
        }

        if(substr($method, 0, 2) == 'is') {
            //isCheckoutActive => return getStoreConfigFlag('payment/nwtkco/checkout_active')
            $key = self::XML_NWTKCO_PATH.$this->_underscore(substr($method,2));
        }  else {
            //removeButtons => return getStoreConfigFlag('payment/nwtkco/remove_buttons')
            $key = self::XML_NWTKCO_PATH.$this->_underscore($method);
        }
        return Mage::getStoreConfigFlag($key,$store);
    }

    protected function _underscore($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }


}
