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
 * Load Klarna Checkout lib files
 */

if(!class_exists('Klarna_Checkout_Order',$autoload=false)) {
    //moved from Checkout.php
    if(!defined('KLARNA_CHECKOUT_DIR')) {
        define('KLARNA_CHECKOUT_DIR', dirname(__FILE__) . '/lib/Checkout');
    }
    require_once KLARNA_CHECKOUT_DIR . '/ConnectorInterface.php';
    require_once KLARNA_CHECKOUT_DIR . '/ResourceInterface.php';
    require_once KLARNA_CHECKOUT_DIR . '/Connector.php';
    require_once KLARNA_CHECKOUT_DIR . '/BasicConnector.php';
    require_once KLARNA_CHECKOUT_DIR . '/Order.php';
    require_once KLARNA_CHECKOUT_DIR . '/Digest.php';
    require_once KLARNA_CHECKOUT_DIR . '/Exception.php';
    require_once KLARNA_CHECKOUT_DIR . '/ConnectionErrorException.php';
    require_once KLARNA_CHECKOUT_DIR . '/ConnectorException.php';
    require_once KLARNA_CHECKOUT_DIR . '/UserAgent.php';

    require_once KLARNA_CHECKOUT_DIR . '/HTTP/TransportInterface.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHandleInterface.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/Request.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/Response.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/Transport.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLTransport.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHeaders.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHandle.php';
    require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLFactory.php';
}


/**
 * Klarna (Checkout) Order Model
 */
class NWT_KCO_Model_Klarna_Order extends Klarna_Checkout_Order
{

    const TEST_URI = 'https://checkout.testdrive.klarna.com/checkout/orders';
    const LIVE_URI = 'https://checkout.klarna.com/checkout/orders';

    protected $_addrFieldMap = array(    //map between Magento and Klarna address fields
        'firstname'=>'given_name',
        'lastname'=>'family_name',
        'street'=>'street_address',
        'city'=>'city',
        'country_id'=>'country',
        'postcode'=>'postal_code',
        'telephone'=>'phone',
        'email'=>'email',
        'prefix'=>'title',
        'care_of'=>'care_of'
    );

    protected $_addrFieldMapShort = array(    //map between Magento and Klarna address fields, for country with address stored in Klarna (not Germany)
        'postcode'=>'postal_code',
        'email'=>'email',
    );



    protected $_testMode    = true;
    protected $_quote       = null;


    public function __construct() {


        $this->_testMode = Mage::helper('nwtkco')->isTestMode();

        //bullshit, this is used only in Klarna_Checkout_Order::create()
        if($this->_testMode) {
            Klarna_Checkout_Order::$baseUri = self::TEST_URI;
        } else {
           Klarna_Checkout_Order::$baseUri = self::LIVE_URI;
        }
        Klarna_Checkout_Order::$contentType = "application/vnd.klarna.checkout.aggregated-order-v2+json";
        $connector = Klarna_Checkout_Connector::create(Mage::helper('nwtkco')->getSharedSecret());
        return parent::__construct($connector);
    }

    public function setTestMode($testMode) {
        $this->_testMode = $testMode;
        return $this;
    }


    public function allowCountries($store = null) {

        $return = array();

        //magento allow countries
        $mageAllowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow', $store));
        //klarna allow countries
        $klrnAllowCountries = $this->getLocale()->getCountries();

        $allowCountries = array_intersect($mageAllowCountries,$klrnAllowCountries);

        //get default country, from config and add as first
        $defaultCountry = Mage::helper('nwtkco')->getCountry($store);

        if($defaultCountry && in_array($defaultCountry,$allowCountries)) {
            $return[] = $defaultCountry;
        }

        //add rest
        foreach($allowCountries as $country) {
            if($country && $country != $defaultCountry) {
                $return[] = $country;
            }
        }
        return $return;
    }




    //change to work with location only as ID (not absolute url), used only in tests
    public function setLocation($location)   {
        if($location && strpos($location,'//') === false) { //if location and does not contains // (not absolute)
            if($this->_testMode) {
                $location = self::TEST_URI.'/'.$location;
            } else {
                $location = self::LIVE_URI.'/'.$location;
            }
        }
        parent::setLocation($location);
        return $this;
    }

    //just add return $this
    public function create(array $data) { parent::create($data); return $this; }
    public function update(array $data) { parent::update($data); return $this; }
    public function parse(array $data)  { parent::parse($data); return $this; }
    public function fetch()             { parent::fetch(); return $this; }
    public function reset()             { $this->setLocation(null);$this->parse(array()); return $this; }

    //getters
    public function isTestMode()    { return $this->_testMode; }
    public function getQuote()      { return $this->_quote; }
    public function getLocale()     { return Mage::getSingleton('nwtkco/klarna_locale'); }


    public function mageAddress($klrnAddr) {


        $return = array();

        if(empty($klrnAddr['street_address']) && !empty($klrnAddr['street_name'])) {
            $klrnAddr['street_address'] =  trim($klrnAddr['street_name'].' '.(!empty($klrnAddr['street_number'])?$klrnAddr['street_number']:''));
        }
        if(!empty($klrnAddr['country'])) {
            $klrnAddr['country'] = strtoupper($klrnAddr['country']);
        }

        foreach($this->_addrFieldMap as $mageField => $klrnField) {
            if(isset($klrnAddr[$klrnField])) {
                $return[$mageField] = $klrnAddr[$klrnField];
            }  else {
                $return[$mageField] = '';
            }
        }
        return $return;
    }


    public function klarnaAddress($mageAddr) {

        $return = array();

        $country = $mageAddr->getCountryId();
        $locale  = $this->getLocale()->getCountry($country);

        if(!$locale) {
            return $return;
        }

        $map = $this->_addrFieldMap;

        if(empty($locale['full-address'])) {
            $map = $this->_addrFieldMapShort;
        } else {
            $map = $this->_addrFieldMap;
        }

        foreach($map as $mageField => $klrnField) {

            if(!$mageAddr->getData($mageField)) continue;

            if($mageField == 'street' && !empty($locale['split-street'])) {
                $street = explode(' ',trim($mageAddr->getStreet(1)));
                if(count($street)>1) {
                    $no = array_pop($street);
                    $return['street_name']   = implode(' ',$street);
                    $return['street_number'] = $no;
                } else {
                    $return['street_name'] = implode(' ',$street);
                    $return['street_number'] = '';
                }
            } else {
                $return[$klrnField] = $mageAddr->getData($mageField);
            }
        }

        //if care_of is not set, but we have company, set it in the care_of field
        if(isset($map['care_of']) && empty($return['care_of']) && $mageAddr->getCompany()) {
            $return['care_of'] = $mageAddr->getCompany();
        }

        return $return;
    }



    public function assignQuote($quote,$validate = true) {

        if($validate) {
            if(!$quote->hasItems()) {
                Mage::throwException(Mage::helper('nwtkco')->__('Empty Cart'));
            }
            if($quote->getHasError()) {
                Mage::throwException(Mage::helper('nwtkco')->__('Cart has errors, cannot checkout'));
            }

            $billingAddress = $quote->getBillingAddress();
            $currency       = $quote->getQuoteCurrencyCode();
            $locale         = $this->getLocale()->getCountry($billingAddress->getCountryId());

            if(!$locale) {
                Mage::throwException(Mage::helper('nwtkco')->__("Invalid country (%s, expecting %s)",$country,implode(', ',$this->getLocale()->getCountries())));
            }

            if($currency != $locale['currency']) {
                Mage::throwException(Mage::helper('nwtkco')->__("Invalid currency (%s, expecting %s)",$currency,$locale['currency']));
            }
        }
        $this->_quote = $quote;
        return $this;
    }



    //fetch order for update (cart items)
    public function fetchForUpdate($location,$expectedStatus = array('checkout_incomplete')) {

        $expectedStatus = (array)$expectedStatus;

        $this->setLocation($location);
        $this->fetch();

        if($expectedStatus) {
            $data = $this->marshal();
            $status = isset($data['status'])?$data['status']:'missing';
            if(!in_array($status,$expectedStatus)) {
                Mage::throwException("Status: {$status}, expect ".implode(', ',$expectedStatus));
            }
        }
        return $this;
    }


   //convert quote items to Klarna cart items

    public function cartItems($quote = null) {

        if(is_null($quote)) {
            $quote = $this->getQuote();
        }

        if(!$quote) {
            return array();
        }


        $billingAddress = $quote->getBillingAddress();
        if($quote->isVirtual()) {
            $shippingAddress = $billingAddress;
        } else {
            $shippingAddress = $quote->getShippingAddress();
        }




         /*Get all cart items*/
        $cartItems = $quote->getAllVisibleItems(); //getItemParentId is null and !isDeleted

        //add products
        $cart            = array();
        $discounts       = array(); //keep all products discounts, by TAX percent


        $row = 1;

        $sendParent = Mage::helper('nwtkco')->sendParent($quote->getStore());

        $maxVat = 0;


        foreach($cartItems as $item) {



            $allItems             = array();
            $bundle               = false;
            $isChildrenCalculated = false;
            $parentQty            = 1;

            //for bundle product, want to add also the bundle, with price 0 if is children calculated
            if($item->getProductType() == 'bundle'  || ($item->hasChildren() && $item->isChildrenCalculated())) {

                $bundle               = true;

                $isChildrenCalculated = $item->isChildrenCalculated();

                $childComment = "(bundle w/{$row})";
                if($isChildrenCalculated) {
                    if(!$sendParent) {
                        $childComment  = null;
                        $parentComment = null;
                    } else {
                        $allItems[] = $item; //list also parent (bundle product);
                        $parentComment         = "Bundle Product,  {$item->getQty()} x {$item->getPriceInclTax()}";
                    }
                    $parentQty = $item->getQty();
                    //this is required only in quote (children qty is not parent * children)
                    //in order is already multiplied
                } else {
                    $allItems[] = $item; //add bundle product
                    $parentComment         = "Bundle Product";
                }

                foreach($item->getChildren() as $child) {
                    if($child->isDeleted()) continue;
                    $allItems[] = $child;
                }
            } else {
                //simple product
                $allItems[] = $item;
            }


            foreach($allItems as $item) {


                $addPrices = true;


                if($bundle) {
                    if(!$item->getParentItemId()) { //main product, add prices if not children calculated
                        $comment = $parentComment;
                        $addPrices = !$isChildrenCalculated;
                    } else { //children, add price only if children calculated
                        $comment = $childComment;
                        $addPrices = $isChildrenCalculated;
                    }
                } else {

                    $comment = array();
                    //add configurable/children information, as comment
                    $options = Mage::helper('catalog/product_configuration')->getOptions($item);
                    if ( $options ) {
                        foreach($options as $option) {
                            if(isset($option['label']) && isset($option['value'])) {
                                $comment[] = $option['label'] . ' : ' . $option['value'];
                            }
                        }
                    }

                    $comment = implode('; ',$comment);
                }

                $vat = $item->getTaxPercent();


                if($addPrices && ($item->getTaxAmount() != 0) && ($vat == 0)) {
                    //calculate vat if not set
                    $tax = $item->getPriceInclTax() - $item->getPrice();
                    if($item->getPrice() != 0 && $tax != 0) {
                        $vat = $tax / $item->getPrice() * 100;
                    }
                }
                $vat = round($vat,0);
                if($vat>$maxVat) $maxVat = $vat;




                //$items with parent id are children of a bundle product;
                //if !$withPrice, add just bundle product (!$getParentId) with price,
                //the child will be without price (price = 0)
                $qty = $item->getQty();
                if($item->getParentItemId()) {
                    //this is required only in quote (children qty is not parent * children)
                    //in order is already multiplied
                    $qty = $qty*$parentQty;
                }

                $cart[] = array(
                    //need also the ID in reference because we could have same products (SKU) multiple times (as configurable child) ... or NOT ?!?
                    //remove id from reference because specter compatibility
                    'reference'     =>$item->getSku(),
                    'name'          =>$item->getName()." ".($comment?"({$comment})":""),
                    'quantity'      =>round($qty,0), //klarna doesn't accept decimal qty?!?
                    'unit_price'    =>$addPrices?round($item->getPriceInclTax() * 100,0):0, //Klarna need prices in cents
                    'discount_rate' =>0, //discount will be set on distinct row
                    'tax_rate'      => intval($vat * 100) //need to be integer, then * 100
                );



                if($addPrices) {




                    //we want discount EXCL. tax, we will add tax later
                    //when catalog price are "INCL. tax", the discount will also contains tax and will be in hiddenTaxAmount
                    //if we have discount but not hiddenTaxAmount then the prices are without taxes
                    //if hidden tax are 0, that means that catalog prices (also the discount) are without taxes
                    //- do not understand that shit :) , but... it's works

                    $discountAmount = $item->getDiscountAmount()-$item->getHiddenTaxAmount();

                    if($discountAmount != 0) {

                            //check if Taxes are applied BEFORE or AFTER the discount
                            if(abs($item->getRowTotalInclTax() - ($item->getRowTotal()+$item->getTaxAmount())) < .001) {
                                //the taxes are applied BEFORE discount; add discount without VAT (is not OK for EU, but, is customer settings
                                $vat =0;
                            }

                            if(!isset($discounts[$vat])) {
                                $discounts[$vat] = 0;
                            }
                            $discounts[$vat] +=  $discountAmount; //keep products discount, per tax percent
                    }
                }

                $row++;
            }
        }


        if(!$quote->isVirtual()) {


            $exclTax    = $shippingAddress->getShippingAmount();
            $inclTax    = $shippingAddress->getShippingInclTax();

            $tax        = $inclTax-$exclTax;


            if($exclTax != 0 && $tax > 0) {
                $vat = $tax /  $exclTax  * 100;
            } else {
                $vat = 0;
            }
            $vat = round($vat,0);
            if($vat>$maxVat) $maxVat = $vat;

            $shippingSKU = trim(Mage::helper('nwtkco')->getShippingSku($quote->getStore()));
            if(!$shippingSKU) {
                $shippingSKU = 'shipping_fee';
            }

            $cart[] = array(
                'type'=>'shipping_fee',
                'reference'=>$shippingSKU,
                'name'=>Mage::helper('nwtkco')->__('Shipping Fee (%s)',$shippingAddress->getShippingDescription()),
                'quantity'=>1,
                'unit_price'=>round($inclTax * 100,0),
                'discount_rate'=>0,
                'tax_rate'=> intval($vat*100),
            );


            //whant discount amount EXCL tax (see @products the explanation)
            //when catalog price are "INCL. tax", the shipping discount will also contains tax and will be in shippinHiddenTaxAmount
            $discountAmount = $shippingAddress->getShippingDiscountAmount()-$shippingAddress->getShippingHiddenTaxAmount();

            if($discountAmount != 0) {

                //check if Taxes are applied BEFORE or AFTER the discount
                if(abs($shippingAddress->getShippingInclTax() - ($shippingAddress->getShippingAmount()+$shippingAddress->getShippingTaxAmount())) < .001) {
                    //the taxes are applied BEFORE discount; add discount without VAT (is not OK for EU, but, is customer settings
                   $vat =0;
                }

                if(!isset($discounts[$vat])) {
                    $discounts[$vat] = 0;
                }
                $discounts[$vat] += $discountAmount;
            }
        }


       //add Discounts (amount are EXCL, see above tax)
       foreach($discounts as $vat=>$amount) {

            if($amount==0) continue;
            if($vat != 0) {
                $amount = $amount*(100+$vat)/100;
            }

            $cart[] = array(
                'type'=>'discount',
                'reference'=>'discount',
                'name'=>Mage::helper('nwtkco')->__('Discount (%s)',$quote->getCouponCode()),
                'quantity'=>1,
                'unit_price'=>round($amount * 100,0)*-1,
                'discount_rate'=>0,
                'tax_rate'=> $vat*100,
            );
        }
        





       //calculate Klarna total
       //WARNING:   The tax must to be applied AFTER discount and to the custom price (not original)
       //           else... the Klarana tax total will differ
       //TODO: Find a method to determine how the discount was applied

       $calculatedTotal = 0;
       $calculatedTax   = 0;
       foreach($cart as $item) {
           //the algorithm used by Klarna seems to be (need to confirm with Klarna)
           //total_price_including_tax = unit_price*quantity; //no round because klarna doesn't have decimals; all numbers are * 100
           //total_price_excluding_tax = total_price_including_tax / (1+taxrate/100000) //is 10000 because taxrate is already multiplied by 100
           //total_tax_amount = total_price_including_tax - total_price_excluding_tax
            $total_price_including_tax = round($item['unit_price'] * $item['quantity'],2);
            if($item['tax_rate'] != 0) {
                $total_price_excluding_tax = round($total_price_including_tax / (1+$item['tax_rate']/10000),0); //   total/1.25
            } else {
                $total_price_excluding_tax = $total_price_including_tax;
            }
            $total_tax_amount = round($total_price_including_tax - $total_price_excluding_tax,0); //round is not required, alreay int
            $calculatedTax   += $total_tax_amount;
            $calculatedTotal += $total_price_including_tax;
       }




       $taxAmount = round($shippingAddress->getTaxAmount()*100,0);

       $diffTaxAmount = $taxAmount-$calculatedTax;
       $difference    =  round($quote->getGrandTotal()*100,0)-$calculatedTotal;



//         Mage::log('Calculated total/taxes: '.$calculatedTotal.' | '.$calculatedTax);
//         Mage::log('Differnece total/taxes: '.$difference.' | '.$diffTaxAmount);
//         Mage::log('Quote total/taxes: '.$quote->getGrandTotal().' | '.$shippingAddress->getTaxAmount());


       $totalsDescription = '';


        //try to find some custom totals
        if(abs($difference) > 100) { //difference seems to be too high, maybe we have some custom totals? Add to description (100 mean 1kr, under this we suppose that is rounding)
            $description = Mage::helper('nwtkco')->__('Other(s)');
            $totals        = $quote->getTotals();
            $ignoreTotals = array('subtotal','shipping','tax','grand_total','discount'); //magento defaults totals
            $totalsDescription = array();

            foreach ($totals as $key => $total) {
                if($total->getValue() == 0 || in_array(strtolower(trim($key)),$ignoreTotals)) continue;
                $totalsDescription[] = "{$total->getTitle()}  x ".round($total->getValue(),2);
            }
            if($totalsDescription) {
                $description .= '[ '.implode('; ',$totalsDescription).']';
            }
        } else {
            $description = Mage::helper('nwtkco')->__('Regulation');
        }


       if(abs($diffTaxAmount) > 1 && $maxVat != 0) { //do not compare with 0; 1 mean 0.01 (all prices are multiplied by 100)
            //if we have difference on taxes, add a correction line with max vat percent
            //we don't know any method to find wich custom totals contains tax and what vat rate
            //use maximum percent
            $diffExclTax = $diffTaxAmount/$maxVat;
            $diffInclTax = $diffExclTax+$diffExclTax*$maxVat/100;
            $unitPrice   = round($diffInclTax*100,0); //need price * 100;
            //check if we have tax, after adding this
            $unitPriceExcludingTax = round($unitPrice / (1+$maxVat/100),0); //   total/1.25
            $tax = $unitPrice-$unitPriceExcludingTax;
            if(abs($tax) > 1) { //do not compare with 0; 1 mean 0.01kr; if vat is 0, don't need to add, we don't got tax regulation
                $cart[] = array(
                    'type'=>'discount',
                    'reference'=>'others_vat',
                    'name'=>$description. "(vat {$maxVat}%)",
                    'quantity'=>1,
                    'unit_price'=>$unitPrice,
                    'discount_rate'=>0,
                    'tax_rate'=> intval($maxVat*100)
                );
                $calculatedTotal += $unitPrice;
            }
       }

       //if tax wasn't fixed above (maxVat = 0), we will still have difference on tax, but... don't know what to do, I don't have a vat percent
       $difference =  round($quote->getGrandTotal()*100-$calculatedTotal,0);
       //if still have difference, probably we have custom totals without taxes

       if(abs($difference) > 0) { //fix totals to match magento total

            //add a line to have same total in Klarna
            $cart[] = array(
                'type'=>'discount',
                'reference'=>'others',
                'name'=>$description,
                'quantity'=>1,
                'unit_price'=>$difference,
                'discount_rate'=>0,
                'tax_rate'=> 0
            );
       }
       //Mage::log($cart);


       return $cart;
    }


    public function merchantInfo() {

        $h = Mage::helper('nwtkco');
        $storeID = Mage::app()->getStore()->getStoreId();
        $merchInfo = array(
            'id'               => $h->getEid(),
            'terms_uri'        => $h->getBuyTermsUri(),
            'checkout_uri'     => $h->getCheckoutUrl(),
            'confirmation_uri' => $h->getCheckoutUrl('confirmation',array('kid'=>'{checkout.order.id}','test'=>$this->_testMode?1:0,'store'=>$storeID)),
            'push_uri'         => $h->getCheckoutUrl('push',array('kid'=>'{checkout.order.id}','test'=>$this->_testMode?1:0,'store'=>$storeID)),
        );

        $minAge = $h->getMinimumAge();
        if($minAge>0) {
            $merchInfo['validation_uri'] = str_ireplace('http://','https://',$h->getCheckoutUrl('validation'));
        }

        return $merchInfo;
    }


    public function customerInfo() {

        if(!($quote = $this->getQuote())) {
            return array();
        }

        $billing    = $quote->getBillingAddress();
        $country    = $billing->getCountryId();
        $locale     = $this->getLocale()->getCountry($country);

        if(!$locale) {
            return array();
        }


        if($this->isTestMode()) {
            if(empty($locale['full-address'])) { //this country, in test mode, use only one test address
                return !empty($locale['test'])?$locale['test']:array();
            }
        }

        if($billing->getPostcode()) {
            //we already have billing address set, keep current address
            return $this->klarnaAddress($billing);
        }


        //try to get the default billing address

        $customer = $quote->getCustomer();
        if(!$customer || !$customer->getId()) {
           if($this->isTestMode() && !empty($locale['test'])) {
                return $locale['test'];
           } else {
                return array();
           }
        }

        $defaultAddress = $customer->getDefaultBillingAddress();
        if(!$defaultAddress || !$defaultAddress->getId() || $defaultAddress->getCountryId() != $locale['country']) {
            //default address is not for current country, cannot use
            if($this->isTestMode() && !empty($locale['test'])) {
                return $locale['test'];
            } else {
                return array();
            }
        }

        $email = $customer->getEmail();

        $return = $this->klarnaAddress($defaultAddress);

        if(empty($return['email']) && $email) {
            $return['email'] = $email;
        }
        return $return;
    }


    public function updateFromQuote() {


        if(!($quote = $this->getQuote())) {
            Mage::throwException(Mage::helper('nwtkco')->__('Missing quote'));
        }

        $data  = $this->marshal();
        $qSign = Mage::helper('nwtkco')->getKlarnaCheckout()->getQuoteSignature($quote);

        if($this->getLocation()) {

            $kSign = !empty($data['merchant_reference']['signature'])?$data['merchant_reference']['signature']:'-1';
            if($qSign == $kSign) {
                //do nothing
                return $this;
            }

        }


        $billingAddress = $quote->getBillingAddress();
        if($quote->isVirtual()) {
            $shippingAddress = $billingAddress;
        }

        $country    = $billingAddress->getCountryId();
        $locale     = $this->getLocale()->getCountry($country);
        if(!$locale) {
            Mage::throwException(Mage::helper('nwtkco')->__("Invalid country (%s, expecting %s)",$country,implode(', ',array_keys($this->_locale))));
        }


        $klarnaCountry = strtoupper(empty($data['purchase_country'])?'KK':$data['purchase_country']);

        //if order was created but we have another country (user change store/country), reset the Klarna order (we will generate another one)
        if($this->getLocation() && $klarnaCountry != $locale['country']) {
            $this->reset();
        }

        $orderData  = array();
        $cart       = array('items'=>$this->cartItems());

        // fix gui
        $gui = array(
            //NOT NEED ANYMORE, KCO iframe is responsive
            //'layout'=> Mage::helper('nwtkco')->isMobile()? 'mobile':'desktop' //accept desktop or mobile
        );

        // add options for auto disable focus 
        if(Mage::helper('nwtkco')->getDisableAutofocus()){
            $gui['options'] = array('disable_autofocus');
        }

        //full order data
        $orderData = array(
            'purchase_country'  => $locale['country'],
            'purchase_currency' => $locale['currency'],
            'locale'            => $locale['locale'],
            'merchant'          => $minfo=$this->merchantInfo(),
            'merchant_reference'=> $mref = array('orderid1'=>'q'.$quote->getId(),'signature'=>$qSign), //set quote id on orderid1, will be replace by order increment id
            'cart'              => $cart,
        );
        
        if($gui){
            $orderData['gui'] = $gui;
        }

        $default_options = $this->_getDefaultOptions();
        if(!empty($default_options)){
            $orderData['options'] = $default_options;
        }

        if( ($customerInfo = $this->customerInfo()) ) {
            $orderData['shipping_address'] = $customerInfo; //billing address is read only (at least in test mode)
        }
        $customer = $quote->getCustomer();
        if($customer && $customer->getId() && $customer->getDob()) {
            $dt = substr($customer->getDob(),0,10); //1973-04-19 00:00:00
            if($dt{0} != 0) {
                $orderData['customer']['date_of_birth'] = $dt;
            }
        }

        if($this->getLocation()) { //already created, just update cart items
            //this will use for update an existent Klarna Order
            try {

                //$this->update(array('cart' => $cart,'gui'=>$gui)); //Klarna do not allow to update the GUI ?!?
                $this->update(array('cart' => $cart,'merchant_reference'=>$mref));
            } catch(Exception $e) {
                Mage::log("[".__METHOD__."] Cannot update existent order {$this->getLocation()}; {$e->getMessage()}");
                Mage::logException($e);
                $this->reset(); //we will try to create another one
            }
        }

        if(!$this->getLocation()) { //new order (or update fail)
            try {
                $this->create($orderData);
            } catch(Exception $e) {

                Mage::log("[".__METHOD__."] Cannot create order: {$e->getMessage()}");
                Mage::logException($e);

                if(isset($orderData['shipping_address']) || isset($orderData['customer'])) {
                    //try to create another one, without address
                    unset($orderData['shipping_address']);
                    unset($orderData['customer']);
                    $this->create($orderData);
                } else {
                    throw $e; //do nothing
                }
            }
        }

        $this->fetch();
        return $this;
    }


    public function registerOrder($orderID) {

        $this->update(array(
                'status' => 'created',
                'merchant_reference' => array('orderid1'=>$orderID)
        ));
        return $this;

    }

    public function cancelReservation() {

        if(!$this->getLocation()) {
            return $this;
        }

        $data = $this->marshal();
        if(empty($data['status']) || $data['status'] != 'checkout_complete' || empty($data['reservation'])) {
            return $this;
        }

        $klarnaAPI = Mage::getModel('nwtkco/klarna_api')->initFromCountry($data['purchase_country'],$this->_testMode);
        $result = $klarnaAPI->cancelReservation($data['reservation']);
        $this->reset();
        if(!$result) {
            Mage::throwException('Failed to cancel reservation, no reason');
        }
    }





    protected function _getDefaultOptions(){
        $helper = Mage::helper('nwtkco');
        $opt = array();

       
       

        // could make a loop, but nevermind!
        if($helper->isNationalIdentificationNumberMandatory() || (int)$helper->getMinimumAge()>0){ //getMinimumAge test also if minimu age is required, else return 0, @see NWT_KCO_Helper_Data
            $opt['national_identification_number_mandatory'] = true;
        }


        if($helper->getAllowSeparateShippingAddress()){
            $opt['allow_separate_shipping_address'] = true;
        }

        if($helper->getShippingDetailsSuccess()){
            $opt['shipping_details'] = $helper->getShippingDetailsSuccess();
        }

        

        // Only hexadecimal values are allowed. The default color scheme will show if no values are set.
        
        if($helper->_hexIsValid($helper->getKcoColorButton())){
              $opt['color_button'] = $helper->getKcoColorButton();
        }

        if($helper->_hexIsValid($helper->getKcoColorButtonText())){
              $opt['color_button_text'] = $helper->getKcoColorButtonText();
        }      
 
        if($helper->_hexIsValid($helper->getKcoColorCheckbox())){
              $opt['color_checkbox'] = $helper->getKcoColorCheckbox();
        }

        if($helper->_hexIsValid($helper->getKcoColorCheckboxCheckmark())){
              $opt['color_checkbox_checkmark'] = $helper->getKcoColorCheckboxCheckmark();
        }

        if($helper->_hexIsValid($helper->getKcoColorHeader())){
              $opt['color_header'] = $helper->getKcoColorHeader();
        }  

        if($helper->_hexIsValid($helper->getKcoColorLinks())){
              $opt['color_link'] = $helper->getKcoColorLinks();
        }
       
       return $opt;
    }

}
