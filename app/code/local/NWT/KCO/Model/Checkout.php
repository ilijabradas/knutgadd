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
 * Klarna (Onepage) Checkout Model
 */
class NWT_KCO_Model_Checkout extends Mage_Checkout_Model_Type_Onepage
{

   protected $_paymentMethod = null;
   protected $_klarnaOrder    = null;
   protected $_allowCountries = array();
   protected $_helper         = null;

   protected $_doNotMarkCartDirty  = false;

   /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct()
    {
        parent::__construct();

        $this->_klarnaOrder    = Mage::getModel('nwtkco/klarna_order');
        $this->_paymentMethod  = Mage::getModel('nwtkco/payment')->getCode();
        $this->_allowCountries = $this->_klarnaOrder->allowCountries();
        $this->_helper         = Mage::helper('nwtkco');
    }


   /**
     * Get frontend checkout session object (alias for Mage_Checkout_Model_Type_Onepage::getCheckout)
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession; //@see Mage_Checkout_Model_Type_Onepage::__construct
    }

    public function getKlarnaOrder()         { return $this->_klarnaOrder; }
    public function allowCountries()         { return $this->_allowCountries; }
    public function isEnabled()              { return $this->_helper->isEnabled(); }


    protected function throwRedirectToCartException($message) { throw new NWT_KCO_Exception($message,'checkout/cart'); }
    protected function throwReloadException($message)         { throw new NWT_KCO_Exception($message,'*/*'); }


    public function setDoNotMarkCartDirty() {   $this->_doNotMarkCartDirty = true; }
    public function getDoNotMarkCartDirty() {   return $this->_doNotMarkCartDirty; }



   public function initCheckout($reloadIfCurrencyChanged = true) {

        $quote  = $this->getQuote();

        $this->checkCart();

        //init checkout
        $customer = $this->getCustomerSession()->getCustomer();
        if ($customer && $customer->getId()) { 
            //$quote->assignCustomer($customer); //this will set also primary billing/shipping address as billing address and don't want that
            $quote->setCustomer($customer);
            //TODO: set customer address
        }

        $allowCountries = $this->allowCountries(); //this will be check in _checkCart

        $blankAddress = $this->getBlankAddress($allowCountries[0]);

        $billingAddress  = $quote->getBillingAddress();
        if($quote->isVirtual()) {
            $shippingAddress = $billingAddress;
        } else {
            $shippingAddress = $quote->getShippingAddress();
        }

        if (!$shippingAddress->getCountryId()) {
            $this->changeCountry($allowCountries[0],$save = false);
        } elseif(!in_array($shippingAddress->getCountryId(),$allowCountries)) {
            $this->getCheckoutSession()->addNotice($this->_helper->__("Klarna checkout is not available for %s, country was changed to %s.",$shippingAddress->getCountryId(),$allowCountries[0]));
            $this->changeCountry($allowCountries[0],$save = false);
        }

        if(!$billingAddress->getCountryId() || $billingAddress->getCountryId() != $shippingAddress->getCountryId()) {
            $this->changeCountry($shippingAddress->getCountryId(),$save = false);
        }

        //set test postcode, some shipping methods need
        if(!$shippingAddress->getPostcode()) {
            $locale     = Mage::getSingleton('nwtkco/klarna_locale')->getCountry($shippingAddress->getCountryId());
            if(!empty($locale['test']['postal_code'])) {
                $shippingAddress->setPostcode($locale['test']['postal_code'])->setCollectShippingRates(true);
            }
        }

        $currencyChanged = $this->checkAndChangeCurrency();

        $payment = $quote->getPayment();



        //force payment method  to our payment method
        $paymentMethod     = $payment->getMethod();
        $shipPaymentMethod = $shippingAddress->getPaymentMethod();

        if(!$paymentMethod || !$shipPaymentMethod || $paymentMethod != $this->_paymentMethod || $shipPaymentMethod != $paymentMethod) {
            $payment->unsMethodInstance()->setMethod($this->_paymentMethod);
            $quote->setTotalsCollectedFlag(false);
            //if quote is virtual, shipping is set as billing (see above)
            //setCollectShippingRates because in onepagecheckout is affirmed that shipping rates could depends by payment method
            $shippingAddress->setPaymentMethod($payment->getMethod())->setCollectShippingRates(true);
        }


        //TODO: ADD MINIMUM AOUNT TEST here

        $method = $this->checkAndChangeShippingMethod();


        $quote->setTotalsCollectedFlag(false)->collectTotals()->save(); //REQUIRED (maybe shipping amount was changed)

        if($currencyChanged && $reloadIfCurrencyChanged) {
            //not needed
            $this->throwReloadException($this->_helper->__('Checkout was reloaded.'));
        }

        if($method === false) {
            Mage::throwException($this->_helper->__('No shipping method'));
        }

        return $this;

   }

    public function checkCart() {

        $quote       = $this->getQuote();
        $redirectUrl = 'checkout/cart';

        //@see Mage_Checkout_Model_Type_Onepage::initCheckout
        if($quote->getIsMultiShipping()) {
                $quote->setIsMultiShipping(false)->removeAllAddresses();
        }
        if(!$this->isEnabled()) {
            $this->throwRedirectToCartException($this->_helper->__('The Klarna Checkout is not enabled, please use an alternative checkout method.'));
        }
        if(!$this->allowCountries()) {
            $this->throwRedirectToCartException($this->_helper->__('The Klarna Checkout is NOT available (no allowed country), please use an alternative checkout method.'));
        }
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->throwRedirectToCartException($this->_helper->__('The cart has no items (or has errors).'));
        }

        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            if(!$error) $error = $this->_helper->__('Subtotal must exceed minimum order amount.');
            $this->throwRedirectToCartException($error);
        }
        return true;
   }

   public function checkAndChangeCurrency() {

        $quote  = $this->getQuote();
        $store  = Mage::app()->getStore();

        $country    = $quote->getBillingAddress()->getCountryId();

        if(!$country) {
            Mage::throwException($this->_helper->__('Country is not set.'));
        }

        $locale     = Mage::getSingleton('nwtkco/klarna_locale')->getCountry($country);

        if(empty($locale['currency'])) {
            Mage::throwException($this->_helper->__('Klarna Checkout is not available for country (%s).',$country));
        }

        $requiredCurrency = $locale['currency'];
        $currentCurrency  = $quote->getQuoteCurrencyCode();


        if($requiredCurrency != $currentCurrency) {
            //try to change the currency (do not make any checks, we will check if currency was applied)

            $store->setCurrentCurrencyCode($requiredCurrency);



            $currency = $store->unsCurrentCurrency()->getCurrentCurrency(); //reload current currency, check if was set (if no rate, the default will be used)
            if($currency->getCode() != $requiredCurrency) {
                $this->throwRedirectToCartException($this->_helper->__('Klarna Checkout requires the currency to be %s but is not available, please use an alternative checkout.',$requiredCurrency));
            }
            $quote->setTotalsCollectedFlag(false);
            if(!$quote->isVirtual() && $quote->getShippingAddress()) {
                $quote->getShippingAddress()->setCollectShippingRates(true);
            }
            $this->getCheckoutSession()->addNotice($this->_helper->__('Currency was changed to %s (cannot use %s for  %s).',$requiredCurrency,$currentCurrency,$locale['name']));


            return true; //currency was changed
            //need to save quote and reload the page?
            //$this->throwReloadException($this->_helper->__('Currency was changed to %s (cannot use %s for  %s).',$requiredCurrency,$currentCurrency,$locale['name']));
        }
        return false; //currency not changed
   }


   public function changeCountry($country,$saveQuote = false) {

        $allowCountries = $this->allowCountries();
        if(!$country || !in_array($country,$allowCountries)) {
            Mage::throwException($this->_helper->__('Invalid country (%s)',$country));
        }

        $blankAddress = $this->getBlankAddress($country);
        $quote        = $this->getQuote();

        $quote->getBillingAddress()->addData($blankAddress);
        if(!$quote->isVirtual()) {
            $quote->getShippingAddress()->addData($blankAddress)->setCollectShippingRates(true);
        }
        if($saveQuote) {
            $this->checkAndChangeCurrency();
            $quote->collectTotals()->save();
        }
   }



   public function getBlankAddress($country) {

        $locale     = Mage::getSingleton('nwtkco/klarna_locale')->getCountry($country);

        $blankAddress = array(
            'customer_address_id'=>0,
            'save_in_address_book'=>0,
            'same_as_billing'=>0,
            'street'=>'',
            'city'=>'',
            'postcode'=>'',
            'region_id'=>'',
            'country_id'=>$country
        );
        return $blankAddress;
   }


   public function  checkAndChangeShippingMethod()
    {

        $quote        = $this->getQuote();
        if($quote->isVirtual()) {
            return true;
        }

        $quote->collectTotals(); //this is need by shipping method with minimum amount

        $shipping = $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
        $allRates = $shipping->getAllShippingRates();

        if(!count($allRates)) {
            return false;
        }

        $rates    = array();
        foreach($allRates as $rate) {
            $rates[$rate->getCode()] = $rate->getCode();
        }


        //test current
        $method = $shipping->getShippingMethod();
        if($method && isset($rates[$method])) {
            return $method;
        }

        //test default
        $method = $this->_helper->getShippingMethod();
        if($method && isset($rates[$method])) {
            $shipping->setShippingMethod($method);//->setCollectShippingRates(true);
            return $method;
        }

        $method = $allRates[0]->getCode();
        $shipping->setShippingMethod($method);//->setCollectShippingRates(true);
        return $method;

    }




   //get (or create in not exists) KlarnaOrder and update from quote

   public function updateKlarnaOrder()
   {

        $quote       = $this->getQuote();

        $klarnaOrder = $this->getKlarnaOrder()->assignQuote($quote);

        //if we already have an order
        $checkoutURI = $this->getCheckoutSession()->getKlarnaOrderUri(); //check session for Klarna Order Uri

        if($checkoutURI) {
            try {
                $klarnaOrder->fetchForUpdate($checkoutURI);
            } catch(Exception $e) {
                $klarnaOrder->reset(); //create new one
                $this->getCheckoutSession()->unsKlarnaOrderUri(); //remove URI from session
                Mage::log("Cannot fetch for update Klarna Order {$checkoutURI}, {$e->getMessage()} (see exception.log)");
                Mage::logException($e);
            }
        }

        $klarnaOrder->updateFromQuote();

        //save klarna uri in checkout/session
        $this->getCheckoutSession()->setKlarnaOrderUri($klarnaOrder->getLocation());

        return $this;

    }






    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return NWT_KCO_Model_Checkout
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote      = $this->getQuote();
        

        $billing         = $quote->getBillingAddress();
        $customerBilling = $billing->exportCustomerAddress();

        $shipping         = $quote->getShippingAddress();


        $customer = Mage::getModel('customer/customer');

        // copy billing data to the customer  (name, email, etc....
        Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);

        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }

        $password = $customer->generatePassword();

        $customer->setPassword($password);
        $customer->setPasswordHash($customer->hashPassword($password));

        $quote
            ->setCheckoutMethod(self::METHOD_REGISTER)
            ->setCustomerIsGuest(false)
            ->setCustomer($customer)
            ->setCustomerId(true);

        return $this;
    }





    protected function _validateKlarnaOrder($klarnaOrder,$quote) {

        //check required fields
        $klarnaData = $klarnaOrder->marshal();

        if(empty($klarnaData['reservation'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing reservation.'));
        }
        if(empty($klarnaData['cart']['items'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, no items.'));
        }
        if(empty($klarnaData['shipping_address'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing shipping_address.'));
        }
        if(empty($klarnaData['billing_address'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing billing_address.'));
        }
        if(empty($klarnaData['purchase_currency'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing purchase_currency.'));
        }
        if(empty($klarnaData['merchant_reference']['signature'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing quote signature.'));
        }


        $qCurrency = strtolower(trim($quote->getQuoteCurrencyCode()));
        $kCurrency = strtolower(trim($klarnaData['purchase_currency']));

        if($qCurrency != $kCurrency) {
            Mage::throwException($this->_helper->__('Quote was changed, Current currency %s, klarna currency %s',$qCurrency,$kCurrency));
        }

        return true;
    }


    public function _getQuoteFromKlarnaOrder($klarnaOrder) {

        $klarnaData = $klarnaOrder->marshal();
        if(empty($klarnaData['merchant_reference']['orderid1'])) {
            Mage::throwException($this->_helper->__('Checkout incomplete, missing quote id.'));
        }

        $qqID = $klarnaData['merchant_reference']['orderid1'];  //ordeid is q12345..
        if($qqID{0} != 'q') {
            Mage::throwException($this->_helper->__('Invalid request, wrong order id %s ',$qqID));
        }
        $qID = (int)substr($qqID,1); //orderid is q12345..
        if($qID<=0) {
            Mage::throwException($this->_helper->__('Invalid request, wrong order id %s',$qqID));
        }
        $quote = Mage::getModel('sales/quote')->load($qID);
        if(!$quote->getId()) {
            Mage::throwException($this->_helper->__('Invalid request, quote %s doesn\'t exists',$qID));
        }
        $klarnaOrder->assignQuote($quote);
        $this->setQuote($quote);

        return $quote;

    }



    //saveOrder; session independent

	public function placeOrder($klarnaOrder,$kID) {

        $this->setDoNotMarkCartDirty(true); //prevent observer to mark quote dirty, we will check here if quote was changed and, if yes, will redirect to checkout

        $quote = $klarnaOrder->getQuote();

        if(!$quote) {
            $quote = $this->_getQuoteFromKlarnaOrder($klarnaOrder);
        }

        $this->_validateKlarnaOrder($klarnaOrder,$quote);

        //this will be saved in order (and is uniq); is ID of push request
        //required to avoid duplicate orders; when push/confirmation are executed concurent
        $quote->setNwtKid($kID);

        $klarnaData = $klarnaOrder->marshal();

        $reservation = $klarnaData['reservation'];

        $cart       = $klarnaData['cart']['items'];
        $shipping   = $klarnaData['shipping_address'];
        $billing    = $klarnaData['billing_address'];

        $diff       = array_diff_assoc($billing,$shipping);

        $billingAddress = $quote->getBillingAddress()
            ->addData($klarnaOrder->mageAddress($billing))
            ->setCustomerAddressId(0)
            ->setSaveInAddressBook(0)
            ->setShouldIgnoreValidation(true);

        $shippingAddress = $quote->getShippingAddress()
            ->addData($klarnaOrder->mageAddress($shipping))
            ->setSameAsBilling(1)
            ->setCustomerAddressId(0)
            ->setSaveInAddressBook(0)
            ->setShouldIgnoreValidation(true);

        $quote->setCustomerEmail($billingAddress->getEmail());

        if(empty($diff)) { //?!?
            $shippingAddress->setSameAsBilling(0);
        }

        $customer      = $quote->getCustomer(); //this (customer_id) is set into self::init
        $isNewCustomer = false;

        if ($customer && $customer->getId()) {
            $quote->setCheckoutMethod(self::METHOD_CUSTOMER)
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerIsGuest(false);
        } else {

             //checkout method
            $quote->setCheckoutMethod(self::METHOD_GUEST)
                ->setCustomerId(null)
                ->setCustomerEmail($billingAddress->getEmail())
                ->setCustomerFirstname($billingAddress->getFirstname())
                ->setCustomerLastname($billingAddress->getLastname())
                ->setCustomerIsGuest(true)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

            //register customer, if required
            if($billingAddress->getEmail()) {
            
                /** @var $customer Mage_Customer_Model_Customer */
                $customer = Mage::getModel('customer/customer')
                    ->setStore($quote->getStore())
                    ->loadByEmail($billingAddress->getEmail());

                if (!$customer->getId() && $this->_helper->registerCustomer()) {
                    $isNewCustomer = true;
                    //this will set checkout method, customer_is_guest, customer etc.
                    $this->_prepareNewCustomerQuote();
                } else {
                    //do nothing; let user guest
                }
            }
        }


        //set payment
        $payment = $quote->getPayment();

        //force payment method
        if(!$payment->getMethod() || $payment->getMethod() != $this->_paymentMethod) {
            $payment->unsMethodInstance()->setMethod($this->_paymentMethod);
        }
        $quote->getPayment()->getMethodInstance()->assignData($klarnaData);

        $quote->setNwtReservation($reservation); //this is used by pushAction

        //- do not recollect totals
        $quote->setTotalsCollectedFlag(true);
        // - or... ????
        //$quote->setTotalsCollectedFlag(true)->collectTotals();

        $qSign = $this->getQuoteSignature($quote);
        $kSign = $klarnaData['merchant_reference']['signature'];

        if($qSign != $kSign) {
            Mage::throwException(Mage::helper('nwtkco')->__("Quote was updated, please review it (check shipping amount, products)"));
        }

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();


        if ($isNewCustomer) {
            try {
                $customer = $quote->getCustomer();
                if($customer->getId()) {
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', '', $quote->getStoreId());
                    } else {
                        $this->getCustomerSession()->setCustomerAsLoggedIn($customer); //login customer
                        $customer->sendNewAccountEmail('registered', '', $quote->getStoreId());
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $order = $service->getOrder();



        try {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                array('order'=>$order, 'quote'=>$quote));
            Mage::dispatchEvent(
                'checkout_submit_all_after',
                array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => null)
            );
            //Do not use this, this is used (for example) by Google Analytics, to set order id in GA block; but we, here, don't have a layout
            //Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order->getId())));
        } catch(Exception $e) {
            //don't care... :D
            Mage::logException($e);
        }



        if($order->getCanSendNewEmailFlag()) {
            try {
                $order->sendNewOrderEmail();
            } catch (Exception $e) {
                Mage::log("Cannot send order email for order {$order->getIncrementId()}, {$e->getMessage()} (see exception.log)");
                Mage::logException($e);
            }
        }

        if($this->_helper->subscribeNewsletter($this->getQuote())) {
            try {
                //subscribe to newsletter
                $this->_subscribeNews($order);
            } catch(Exception $e) {
                Mage::log("Cannot subscribe customer ($email) to the Newsletter: ".$e->getMessage());
            }
        }

        return $order;
    }


    protected function _subscribeNews($order)
    {

        $email = $order->getCustomerEmail();

        if(!$email) {
            return false;
        }

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
        if($subscriber->getId()) {
            return false;
        }

        return $subscriber->subscribe($email);
    }




    public function getQuoteSignature($quote = null) {

        if(!$quote) {
            $quote = $this->getQuote();
        }

        $billingAddress = $quote->getBillingAddress();
        if(!$quote->isVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
        }

        $info = array(
            //'store'   =>$quote->getStore()->getId(), -- with a wrong cookie (store), magento will reset store when we choose default store
            'currency'=>$quote->getQuoteCurrencyCode(),
            //'customer'=>$quote->getCustomer()->getId(), -- this is updated when quote is placed, could be changed
            'shipping_method'=>$quote->isVirtual()?null:$shippingAddress->getShippingMethod(),
            'shipping_country' =>$quote->isVirtual()?null:$shippingAddress->getCountryId(),
            'billing_country' =>$billingAddress->getCountryId(),
            'payment' =>$quote->getPayment()->getMethod(),
            'subtotal'=>sprintf("%.2f",round($quote->getBaseSubtotal(),2)), //store base (currency will be set in checkout)
            'total'=>sprintf("%.2f",round($quote->getBaseGrandTotal(),2)),  //base grand total
            'items'=>array()
        );

        foreach($quote->getAllVisibleItems() as $item) {
            $info['items'][$item->getId()] = sprintf("%.2f",round($item->getQty()*$item->getBasePriceInclTax(),2));
        }
        ksort($info['items']);
        return md5(serialize($info));

    }

    public function cancelKlarnaReservation() {
        $this->getCheckoutSession()->unsKlarnaOrderUri(); //remove klarna URI from session
        $this->getKlarnaOrder()->cancelReservation();
    }



}

