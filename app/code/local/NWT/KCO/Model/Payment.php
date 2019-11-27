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
 * Klarna Checkout Payment method 
 */
class NWT_KCO_Model_Payment extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'nwtkco';
    protected $_formBlockType = 'nwtkco/payment_form';
    protected $_infoBlockType = 'nwtkco/payment_info';

   /**
     * Payment Method features
     * @var bool
     */
    protected $_isGateway                   = false;
    protected $_canOrder                    = false;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;  //capture when place invoce
    protected $_canCapturePartial           = false; //NOT implemented (yet)
    protected $_canCaptureOnce              = true;
    protected $_canRefund                   = true;  //refund on credit memo
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = false;  //implemented (could be set true), but do not want to enable
                                                      //we do not want "VOID payment", the payment could be voided by canceling the order (NOT implemented)
    protected $_canUseInternal              = false;  //cannot be used internal (backend)
    protected $_canUseCheckout              = true;   //used in checkout (will redirect the user to the /klarna/checkout)
    protected $_canUseForMultishipping      = false;  //cannot be used for multishipping
    protected $_isInitializeNeeded          = true;   //will use initialize to authorize and set state/status to new/pending (with authorize the state is set to processing)
    protected $_canFetchTransactionInfo     = false;
    protected $_canReviewPayment            = false;
    protected $_canCreateBillingAgreement   = false;
    protected $_canManageRecurringProfiles  = false; // cannot
    protected $_canCancelInvoice            = true;



   /**
     * Check whether payment method can be used
     * changed  to also check if checkout is enabled
     *
     *
     * @param Mage_Sales_Model_Quote|null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        //check if checkout is enabled
        if(!Mage::helper('nwtkco')->isEnabled()) {
            return false;
        }
        return parent::isAvailable($quote);
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  NWT_KCO_Model_Payment
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $expires_dt = null;

        if($data->getExpiresAt()) {
            $expires = @strtotime($data->getExpiresAt());
            if($expires) {
                $expires_dt = date("Y-m-d H:i:s",$expires);
            }
        }

        $this->getInfoInstance()
            ->setKlarnaReservation($data->getReservation())
            ->setKlarnaId($data->getId())
            ->setKlarnaTest(Mage::helper('nwtkco')->isTestMode()?1:0)
            ->setKlarnaExpiresAt($expires_dt);

        return $this;
    }


    /**
     * To check billing country is allowed for the payment method
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        $country = trim(strtoupper($country));
        return $country && in_array($country,Mage::helper('nwtkco')->getCountries());
    }




    protected $_quote = null;

    public function canUseForCurrency($currency)
    {
        if(is_null($this->_quote)) {
            //cannot use that because we need currency instead of baseCurrency (magento default), also need to check togheter with country
            //the payment need to be validated via isApplicableToQuote
            return true; //we will assume that in quote was validated
        }

        $country = $this->_quote->getBillingAddress()->getCountryId();
        if(!$country) {
            return false;
        }

        $locale = Mage::getSingleton('nwtkco/klarna_locale')->getCountry($country);
        if(empty($locale['currency'])) {
            return false;
        }

        $currentCurrency = $this->_quote->getQuoteCurrencyCode();

        $store = $this->_quote->getStore();
        $available = $store->getAvailableCurrencyCodes();

        if($locale['currency'] == $currency  || $locale['currency'] == $currentCurrency) {
            //base or current quote currency, no convert need
            return true;
        }


        if(!in_array($locale['currency'],$available)) {
            return false;
        }

        //check if required currency can be used (have rate for it,
        //@see Mage_Core_Model_Store::getCurrentCurrencyCode), if a rate doesn't exists, the currency will be changed to the base
        $currency = Mage::getModel('directory/currency')->load($locale['currency']);
        if(!$currency || !$currency->getId()) {
            return false;
        }

        //check if we can convert
        if( !($rate = $store->getBaseCurrency()->getRate($currency)) ) { // =!==
            //KlarnaCheckout we will try to set required currency, if we don't have a rate, this will not be possible
            //@see Mage_Core_Model_Store::getCurrentCurrencyCode), if a rate doesn't exists, the currency will be changed to the base
            return false;
        }
        return true;
    }


   /**
     * Check whether payment method is applicable to quote
     * Purposed to allow use in controllers some logic that was implemented in blocks only before
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|null $checksBitMask
     * @return bool
     */
    public function isApplicableToQuote($quote, $checksBitMask)
    {

        $this->_quote = $quote; //this will be used by check currency
        $result = parent::isApplicableToQuote($quote,$checksBitMask);
        $this->_quote = null;

        return $result;

    }



    /**
     * Checkout redirect URL getter for onepage checkout
     *
     * @see Mage_Checkout_OnepageController::savePaymentAction()
     * @see Mage_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::helper('nwtkco')->getCheckoutUrl();
    }



    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return self::ACTION_AUTHORIZE;
    }


    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * We use this because want to set state to new/pending, insteas of processing
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function initialize($paymentAction, $stateObject)
    {
        //$paymentAction not used, we will "authorize" by default

        $payment = $this->getInfoInstance();

        $order   = $payment->getOrder();

        $payment->authorize(true,$order->getBaseTotalDue());
        $payment->setAmountAuthorized($order->getTotalDue());


        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        
        $stateObject->setState($orderState);
        
        $orderStatus = $this->getConfigData('order_status');

        if (!$orderStatus) {
            $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
        } else {
        
            //check which state we have (NEW or PROCESSING)

            $statuses = $order->getConfig()->getStateStatuses($orderState);
            if(!isset($statuses[$orderState])) {
                //check if we have  "processing" status
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $statuses = $order->getConfig()->getStateStatuses($orderState);
                if(isset($statuses[$orderStatus])) {
                    //set state = processing
                    $stateObject->setState($orderState);
                }
            }
        }

        $stateObject->setStatus($orderStatus);
        $stateObject->setIsNotified(false);

        return $this;

    }


   /**
     * Authorize payment abstract method
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return self
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if (!$this->canAuthorize()) {
            Mage::throwException(Mage::helper('payment')->__('Authorize action is not available.'));
        }
        $info = $this->getInfoInstance();
        $payment->setTransactionId($info->getKlarnaReservation());
        $payment->setIsTransactionClosed(0); //let transaction OPEN (need to cancel/void this reservation)
        $payment->setMessage(Mage::helper('nwtkco')->__('Klarna Reservation %s was CREATED.',$payment->getTransactionId()));
        return $this;
    }





    public function capture(Varien_Object $payment, $amount) {

        //NOTE: amount is "baseAmount"

        $authTransaction = $payment->getAuthorizationTransaction();
        if($authTransaction) {
            $rno = $authTransaction->getTxnId();
            $payment->setParentTransactionId($rno);
        } else {
            $rno  = $this->getInfoInstance()->getKlarnaReservation();
        }

        if(!$rno) {
            //do nothing
            return $this;
        }

        try {
            $klarnaAPI = Mage::getModel('nwtkco/klarna_api')->initFromOrder($payment->getOrder());
            $result = $klarnaAPI->activate($rno);
        } catch(Exception $e) {
            Mage::logException($e);
            //need to throw MageException, to be shown as error
            Mage::throwException(Mage::helper('nwtkco')->__('The Klarna order cannot be activated: %s.',$e->getMessage()?$e->getMessage():get_class($e)));
        }

        $payment->setTransactionId($result[1]);
        $payment->setIsTransactionClosed(1);
        $payment->setMessage(Mage::helper('nwtkco')->__('Klarna Reservation %s was ACTIVATED; Risk: %s',$rno,$result[0]));
        return $this;

    }



    /**
     * Refund specified amount for payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {

        if (!$this->canRefund()) {
            Mage::throwException(Mage::helper('payment')->__('Refund action is not available.'));
        }
        $ino = $payment->getRefundTransactionId();
        if(!$ino) {
            Mage::throwException(Mage::helper('nwtkco')->__('Unknown transaction.'));
        }
        try {
            $klarnaAPI = Mage::getModel('nwtkco/klarna_api')->initFromOrder($payment->getOrder());
            $result = $klarnaAPI->creditInvoice($ino); //Klarna will responde with credited invoice number (not with the new invoice number), this sux
        } catch(Exception $e) {
            Mage::logException($e);
            //need to throw MageException, to be shown as error
            Mage::throwException(Mage::helper('nwtkco')->__('The Klarna order cannot be refunded: %s.',$e->getMessage()?$e->getMessage():get_class($e)));
        }
        $payment->setTransactionId($result.'-refund')
                ->setIsTransactionClosed(1)
                ->setMessage(Mage::helper('nwtkco')->__('Klarna Invoice %s was refunded.',$ino));
        return $this;
    }


   /**
     * Cancel payment 
     *
     * @param Varien_Object $payment
     *
     * @return self
     */
    public function cancel(Varien_Object $payment)
    {    
        return $this->void($payment);
    }
    
    
    /**
     * Void payment 
     *
     * @param Varien_Object $payment
     *
     * @return self
     */
    public function void(Varien_Object $payment)
    {


        if (!$this->canVoid($payment)) {
            Mage::throwException(Mage::helper('payment')->__('Void action is not available.'));
        }

        $authTransaction = $payment->getAuthorizationTransaction();
        if($authTransaction) {
            $rno = $authTransaction->getTxnId();
            $payment->setParentTransactionId($rno);
        } else {
            $info = $this->getInfoInstance();
            $rno  = $this->getInfoInstance()->getKlarnaReservation();
        }
        if(!$rno) {
            //do nothing
            return $this;
        }

        Mage::log($rno);

        try {
            $klarnaAPI = Mage::getModel('nwtkco/klarna_api')->initFromOrder($payment->getOrder());
            $result = $klarnaAPI->cancelReservation($rno);
        } catch(Exception $e) {
            Mage::logException($e);
            //need to throw MageException, to be shown as error
            Mage::throwException(Mage::helper('nwtkco')->__('The Klarna order cannot be canceled, %s.',$e->getMessage()?$e->getMessage():get_class($e)));
        }

        Mage::log($result);

        if(!$result) {
            Mage::throwException(Mage::helper('nwtkco')->__("Cannot cancel Klarna Reservation %s.",$rno));
        }

        $payment->setTransactionId($rno.'-cancel');
        $payment->setMessage(Mage::helper('nwtkco')->__("Klarna reservation %s was CANCELED.",$rno));

        return $this;

    }

    
    /**
     * Detach payment - this will just "detach" the payment from order, reservation will not be canceled
     * Not USED yet (need to add action in order controller)
     *
     * @param Varien_Object $payment
     *
     * @return self
     */
    public function detach(Varien_Object $payment)
    {
     
        $authTransaction = $payment->getAuthorizationTransaction();
        if(!($authTransaction && ($rno = $authTransaction->getTxnId()))) {
            //not transaction to detach
            return $this;
        }

        //check if we have invoice in pending states
        $order = $payment->getOrder();
        $invoices = $order->getInvoiceCollection();
        $openInvoices = 0;
        foreach($invoices as $invoice) {
            if($invoice->canCancel()) { //$invoice->getState() == self::STATE_OPEN;
                $openInvoices++;
            }
        }
        if($openInvoices>0) {
            Mage::throwException(Mage::helper('nwtkco')->__("Klarna reservation %s cannot be detached. You have %s pending invoice(s).",$rno,$openInvoices));
        }

        //detach the authorization (close the transaction)
        $payment
            ->setParentTransactionId($rno)
            ->setTransactionId($rno.'-void')
            ->setMessage(Mage::helper('nwtkco')->__("Klarna reservation %s was detached from the order.<br />All (Klarna) payment operations from now on (activating the order, invoicing etc.) have to be performed offline (on the Klarna Site).<br />",$rno));

        return $this;

    }

}
