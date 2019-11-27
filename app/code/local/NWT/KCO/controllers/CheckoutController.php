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
 * Klarna onepage controller
 */
class NWT_KCO_CheckoutController extends Mage_Checkout_Controller_Action
{

    protected $_checkout = null;


    protected function getKlarnaCheckout()
    {
        return Mage::helper('nwtkco')->getKlarnaCheckout();
    }


    protected function _getSession()
    {
         return Mage::getSingleton('checkout/session');
    }

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }


    public function preDispatch()
    {

        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $skipActions = array('thankyou','confirmation','push');
        if(in_array($action,$skipActions)) {
            return $this;
        }


        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = $this->_getSession()->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        return $this;
    }



    public function indexAction()
    {
        try {

            //init/check checkout
            $checkout = $this->getKlarnaCheckout()->initCheckout($reloadIfCurrencyChanged = false);

            //Update Klarna iframe
            $checkout->updateKlarnaOrder();
            Mage::register('KlarnaOrder',$checkout->getKlarnaOrder());

        } catch(NWT_KCO_Exception $e) {
            if($e->isReload()) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
            }
            if($e->getRedirect()) {
                $this->_redirect($e->getRedirect());
                return;
            }
        } catch(Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__('Cannot initialize checkout (%s)',get_class($e)));
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__('Cannot initialize the Klarna Checkout (%s)',get_class($e)));
            Mage::log("[".__METHOD__."] (".get_class($e).") {$e->getMessage()} ");
            Mage::logException($e);
        }

        $this->_getSession()->setCartWasUpdated(false);
        $this->loadLayout();
        $this->_initLayoutMessages(array('checkout/session'));
        $title = Mage::helper('nwtkco')->getTitle();
        $this->getLayout()->getBlock('head')->setTitle($title?$title:$this->__('Klarna Checkout'));
        $this->renderLayout();

    }



    protected function _sendResponse($blocks = null,$updateCheckout = true) {


        $response = array();

        //reload the blocks even we have an error
        if(is_null($blocks)) {
            $blocks = array('shipping_method','cart','discount','messages', 'klarna','newsletter','customercomment');
        } elseif($blocks) {
            $blocks = (array)$blocks;
        } else {
            $blocks = array();
        }

        if(!in_array('messages',$blocks)) {
            $blocks[] = 'messages';
        }


        $updateKlarna = false;
        if($updateCheckout) {
            $key = array_search('klarna', $blocks);
            if($key !== false) {
                $updateKlarna = true;
                unset($blocks[$key]); //this will be set later
            }
        }


        if($updateCheckout) {  //if blocks contains only "messages" do not update


            try {

                $checkout = $this->getKlarnaCheckout()->initCheckout();

                //set new quote signature
                $response['ctrlkey'] = $checkout->getQuoteSignature();

                if($updateKlarna) {
                    //update klarna iframe
                    $checkoutURI = $this->_getSession()->getKlarnaOrderUri();
                    $klarnaOrder = $checkout->updateKlarnaOrder()->getKlarnaOrder();
                    Mage::register('KlarnaOrder',$klarnaOrder);
                }

            } catch(NWT_KCO_Exception $e) {
                if($e->isReload()) {
                    $response['reload'] = 1;
                    $response['messages'] = $e->getMessage();
                    $this->_getSession()->addNotice($e->getMessage());
                } elseif($e->getRedirect()) {
                    $response['redirect'] = $e->getRedirect();
                    $response['messages'] = $e->getMessage();
                    $this->_getSession()->addError($e->getMessage());
                } else {
                    $this->_getSession()->addError($e->getMessage());
                }
            } catch(Mage_Core_Exception $e) {
                //do nothing, we will just show the message
                $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__('Cannot update checkout (%s)',get_class($e)));
            }  catch(Exception $e) {
                $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__('Cannot initialize Klarna Checkout (%s)',get_class($e)));

                $this->_getSession()->addError($e->getMessage()?$e->getMessage():get_class($e));
                Mage::log("[".__METHOD__."] {$e->getMessage()} ".get_class($e));
                Mage::logException($e);
            }


            if(!empty($response['redirect'])) {
                if($this->getRequest()->isXmlHttpRequest()) {
                    $response['redirect'] = Mage::getUrl($response['redirect']);
                    $this->getResponse()->setBody(Zend_Json::encode($response));
                } else {
                    $this->_redirect($response['redirect']);
                }
                return;
            }


            if($updateKlarna &&  (empty($klarnaOrder) || $klarnaOrder->getLocation() != $checkoutURI)) {
                //another klarna order was created, add klarna block (need to be reloaded)
                $blocks[] = 'klarna';
            }

        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->loadLayout('nwtkco');
            $this->_initLayoutMessages(array('checkout/session'));
            foreach($blocks as $id) {
                $name = "nwtkco_{$id}";
                $block = $this->getLayout()->getBlock($name);
                if($block) {
                    $response['updates'][$id] = $block->toHtml();
                }
            }
            $this->getResponse()->setBody(Zend_Json::encode($response));
        } else {
            $this->_redirect('*/*');
        }
    }



    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {


        if(!$this->getRequest()->isXmlHttpRequest()) {
            return false;
        }
        //check if quote was changed
        $ctrlkey    = (string)$this->getRequest()->getParam('ctrlkey');
        if(!$ctrlkey) {
            return false; //do not check
        }

        //check if cart was updated
        $currkey    = $this->getKlarnaCheckout()->getQuoteSignature();
        if($currkey != $ctrlkey) {
            $response = array(
                'reload'   => 1,
                'messages' => $this->__('The cart was updated (from another location), reloading the checkout, please wait...')
            );
            $this->_getSession()->addError($this->__('The requested changes were not applied. The cart was updated (from another location), please review the cart.'));
            $this->getResponse()->setBody(Zend_Json::encode($response));
            return true;
        }

        return false;
    }





    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        $shippingMethod = $this->getRequest()->getPost('shipping_method', '');
        try {
            $quote = $this->getKlarnaCheckout()->getQuote();
            if($shippingMethod && ($rate  = $quote->getShippingAddress()->getShippingRateByCode($shippingMethod))) {
                $quote->getShippingAddress()->setShippingMethod($shippingMethod);
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',array('request'=>$this->getRequest(),'quote'=>$quote));
                $quote->collectTotals()->save();
            } else {
                $this->_getSession()->addError($this->__('Invalid shipping method (%s).',$shippingMethod?$shippingMethod:'missing'));
            }
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage()?$e->getMessage():get_class($e));
            Mage::log("[".__METHOD__."] (".get_class($e).") {$e->getMessage()} ");
            Mage::logException($e);
        }
        $this->_sendResponse();
    }


     /**
     * Initialize coupon
     */
    public function saveCommentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $quote = $this->getKlarnaCheckout()->getQuote();
        $origComment = trim((string)$this->getRequest()->getParam('kco_customer_comment'));
        $comment = substr(strip_tags($origComment),0,255);


        try {
            if($comment != $origComment) {
                $this->_getSession()->addNotice($this->__('Your comment has been reduced to 255 characters (HTML has also been stripped down). Thank you!'));
            }
            $quote->setCustomerNote($comment)->setCustomerNoteNotify(false);
            $quote->save();
            $this->_getSession()->addSuccess($this->__('Your comment has been saved.'));
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__("Cannot save the comment (%s)",get_class($e)));
            Mage::log("[".__METHOD__."] (".get_class($e).") {$e->getMessage()} ");
            Mage::logException($e);
        }
        $this->_sendResponse('customercomment',$updateCheckout = false);
        return;
    }

    public function saveCouponAction()
    {

        if ($this->_expireAjax()) {
            return;
        }


        $quote = $this->getKlarnaCheckout()->getQuote();

        $couponCode    = (string)$this->getRequest()->getParam('coupon_code');
        $oldCouponCode = $quote->getCouponCode();
        $remove        = (int)$this->getRequest()->getParam('remove') > 0;

        if($remove) {
            $couponCode    = '';
        } elseif($couponCode) {

            $codeLength = strlen($couponCode);
            if($codeLength > 255) {
                //invalid
                $couponCode = '';
            }
        }

        if(!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_getSession()->addError($this->__('Coupon code is not valid (or missing)'));
            return $this->_sendResponse();
        }



        try {

            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode($couponCode)->collectTotals()->save();

            if($couponCode) {
                if ($couponCode == $quote->getCouponCode()) {
                    $this->_getSession()->addSuccess($this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode)));
                } else {
                    $this->_getSession()->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode)));
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }

        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__("Cannot apply coupon (%s)",get_class($e)));
            Mage::log("[".__METHOD__."] (".get_class($e).") {$e->getMessage()} ");
            Mage::logException($e);
        }
        $this->_sendResponse();
        return;
    }


    //remove item from cart
    public function deleteAction() {

        if ($this->_expireAjax()) {
            return;
        }

        $id = (int) $this->getRequest()->getParam('id');

        if ($id > 0) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $this->_getSession()->addSuccess('The item was removed');
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        } else {
            $this->_getSession()->addNotice($this->__('Invalid item.'));
        }

        $this->_sendResponse();
        return;
    }


    //subscribe to newsletter
    public function updateNewsletterAction() {
        if ($this->_expireAjax()) {
            return;
        }
        $newsletter = (int) $this->getRequest()->getParam('newsletter',0);
        $quote = $this->_getSession()->getQuote();
        if($quote->getPayment()) {
            $quote->getPayment()->setAdditionalInformation('nwtkco_newsletter',$newsletter>0?1:-1)->save();
        }
        $this->_sendResponse('newsletter',$updateCheckout = false);
        return;
    }



    //this is not ajax

    public function saveCountryAction() {

        $checkout = $this->getKlarnaCheckout();
        $country  = $this->getRequest()->getParam('country');
        $allowCountries = $checkout->allowCountries();

        if($country && in_array($country,$allowCountries)) {
            try {
                $checkout->changeCountry($country, $saveQuote = true);
            } catch(NWT_KCO_Exception $e) {
                if($e->getRedirect()) {
                    $this->_getSession()->addNotice($e->getMessage());
                    $this->_redirect($e->getRedirect());
                    return;
                } else {
                    $this->_getSession()->addError($e->getMessage());
                }
            } catch(Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch(Exception $e) {
                $this->_getSession()->addError($e->getMessage()?$e->getMessage():$this->__('Cannot initialize the Klarna Checkout (%s)',get_class($e)));
                Mage::log("[".__METHOD__."] (".get_class($e).") {$e->getMessage()} ");
                Mage::logException($e);
            }
        } else {
            $this->_getSession()->addError($this->__('Invalid country'));
        }
        $this->_redirect('*/*');
    }


    /*   CONFIRMATION/PUSH (place order) PART */

    const ERR_KLARNA_FETCH              = 100; //cannot fetch klarna order
    const ERR_KLARNA_INVALID            = 110; //invalid klarna response (missing required field)
    const ERR_KLARNA_STATUS             = 120; //not expected status (checkout_complete/created)
    const ERR_KLARNA_ORDER_CANCELED     = 200; //cannot place order, reservation was canceled
    const ERR_KLARNA_ORDER_NOT_CANCELED = 210; //cannot place order, reservation was NOT canceled


    protected function _placeOrder($kID,$test,$store,$prefix="") {

        //Yea, i know that this is too complicated, but... at this moment I don't find a simpler solution
        //we want to place order one of confirmation or push action, which came first, and use the result in the other one

        Mage::app()->setCurrentStore($store);


        //get (or create if not exists) the request for $kID
        //the request will contains (fetch) also the klarna data (in marshal field)

        try {
            $pushRequest = NWT_KCO_Model_Push::getRequest($kID,$test,$store,$prefix);
            if($pushRequest->getIsAlreadyFetched()) {
                $_fetched = ' (already fetched)';
            } else {
                $_fetched = ' ; Klarna data was fetched';
            }
            if($pushRequest->getIsAlreadyStarted()) {
                $this->_logPush("Found Push history ID {$pushRequest->getId()} for KID {$kID}{$_fetched}",$prefix);
            } else {
                $this->_logPush("Push history ID {$pushRequest->getId()} for KID {$kID} was just CREATED{$_fetched}",$prefix);
            }
        } catch(Exception $e) {
            $this->_logPush("Failed to fetch request, {$e->getMessage()}",$prefix);
            throw new Exception($e->getMessage(),self::ERR_KLARNA_FETCH);
        }

        $klarnaData = unserialize($pushRequest->getMarshal());

        //if we have a processed request, and the result was an error... try to cancel klarna reservation (if not) and stop here
        if($pushRequest->getError()) {
            $this->_logPush("Already processed request, we have error [{$pushRequest->getError()}], {$pushRequest->getErrorMsg()}",$prefix);
            //refetch klarna order; if still exists we will cancel
            try {
                $klarnaOrder = Mage::getModel('nwtkco/klarna_order')->setTestMode($test>0)->fetchForUpdate($kID,null);
                $this->_logPush("Klarna order found (refetched)",$prefix);
                $klarnaOrder->cancelReservation();
                $this->_logPush("Reservation was canceled",$prefix);
            } catch(Exception $e) {
                $this->_logPush("Cannot refetch and cancel Klarna order (was previous canceled?), {$e->getMessage()}",$prefix);
            }
            throw new Exception($pushRequest->getErrorMsg(),$pushRequest->getError());
        }

        $err        = null;
        $errCode    = 0;

        //check klarna order status/reservation
        if(empty($klarnaData['status'])) {
            $err     = $this->__('Invalid Klarna response (missing status)');
            $errCode = self::ERR_KLARNA_INVALID;
        } elseif(!in_array($klarnaData['status'],array('checkout_complete','created'))) {
            $err     = $this->__('Invalid status %s, expect checkout_complete or created',$klarnaData['status']);
            $errCode = self::ERR_KLARNA_STATUS;
        } elseif(empty($klarnaData['reservation'])) {
            $err     = $this->__('Invalid Klarna response (missing reservation)');
            $errCode = self::ERR_KLARNA_INVALID;
        }

        if($errCode) {
            try {
                $pushRequest->setError($errCode)->setErrorMsg($err)->save();
            } catch(Exception $e) {
                $this->_logPush("Error [{$errCode}], [$err]; cannot update push request",$prefix);
                Mage::log(__METHOD__."{$prefix} Cannot update push request {$pushRequestID}, {$e->getMessage()} (see exception.log)");
                Mage::logException($e);
            }
            throw new Exception($err,$errCode);
        }

        $prefix = "{$prefix}] [({$klarnaData['status']},{$klarnaData['reservation']})";


        //order was already placed ?
        $order = Mage::getModel('sales/order')->load($klarnaData['reservation'],'nwt_reservation');

        if(!$order->getId() && $pushRequest->getIsAlreadyStarted() && $pushRequest->getAge()<5) {
            //if NO order was placed, but the request alreay exists and is started soon, wait a moment for the other process
            $pushID = $pushRequest->getId();
            $this->_logPush("Push request is already started ({$pushRequest->getAge()} minutes old) but we still don\'t have an order, wait 5 seconds",$prefix);
            sleep(5); //wait other to finish
            $this->_logPush('continue',$prefix);

            //reload request; check we now have an error
            $pushRequest->load($pushID);
            if($pushRequest->getError()) {
                $this->_logPush("Other process was finished with error [{$pushRequest->getError()}], {$pushRequest->getErrorMsg()}",$prefix);
                throw new Exception($pushRequest->getErrorMsg(),$pushRequest->getError());
            } else {
                $this->_logPush('Push request was reloaded, no error, recheck if now have an order',$prefix);
            }
            //recheck order, maybe now is finished
            $order = Mage::getModel('sales/order')->load($klarnaData['reservation'],'nwt_reservation');
        }

        //recreate klarnaOrder from request
        $klarnaOrder = Mage::getModel('nwtkco/klarna_order')->setTestMode($test>0)->setLocation($kID)->parse($klarnaData);


        if(!$order->getId()) {

            //no order place, no error, try to place order

            $checkout = $this->getKlarnaCheckout();
            $this->_logPush("Order not found, try to place/create",$prefix);
            //try to place order
            $order = null;
            $orderException = null;
            try {
                $order = $checkout->placeOrder($klarnaOrder,$pushRequest->getId());
                $this->_logPush("Order successfully created {$order->getIncrementId()}",$prefix);
                $order->setIsAlreadyPlaced(false);
            } catch(Exception $orderException) {
                Mage::log("Cannot place order for Klarna order {$kID}; reservation {$klarnaData['reservation']}, {$orderException->getMessage()}");
                Mage::logException($orderException);
                $this->_logPush("Order cannot be placed, {$orderException->getMessage()}",$prefix);

                //recheck is we still do not have already placed order
                //if order is already place; we will got duplicate error when trying to replace same quote (because of nwt_kid/increment_id unique fields)
                $order = Mage::getModel('sales/order')->load($klarnaData['reservation'],'nwt_reservation');
                if(!$order->getId()) {
                    //set error on push request
                    $errCode = (int)$orderException->getCode();
                    try {
                        //update the request with error code/message (we will do not try, later, to place order)
                        $pushRequest->setError($errCode?$errCode:-1)->setErrorMsg($orderException->getMessage())->save();
                    } catch(Exception $e) {
                        $this->_logPush("Cannot update push request with latest error, $e->getMessage()",$prefix);
                        Mage::logException($orderException);
                    }

                    //save quote
                    $quote = $klarnaOrder->getQuote();
                    if($quote && $quote->getId()) {
                        //save quote
                        //    -if order was saved, need to save "active" field
                        //    -if order was not saved, need to save customer addresses
                        try {
                            $quote->save(); //try to save addresses & other info to the quote
                        } catch(Exception $e) {
                            $this->_logPush("Cannot save quote {$quote->getId()} (save addresses), {$e->getMessage()}",$prefix);
                            Mage::log(__METHOD__."  Klarna reservation {$klarnaData['reservation']}, quote {$quote->getId()}; Order was NOT created, cannot save the quote (addresses), {$e->getMessage()}");
                            Mage::logException($e);
                        }
                    }

                    //cancel reservation?
                    try {
                        $klarnaOrder->cancelReservation();
                        $this->_logPush("Reservation was canceled.",$prefix);
                    } catch(Exception $e) {
                        $this->_logPush("Fail to cancel reservation, {$e->getMessage()}.",$prefix);
                        Mage::logException($e);
                    }
                    throw $orderException;
                }
                $this->_logPush("Found an already placed (by another request) order {$order->getIncrementId()}, use this one",$prefix);
                $order->setIsAlreadyPlaced(true);
            }
        } else {
            $this->_logPush("Found order {$order->getIncrementId()}",$prefix);
            $order->setIsAlreadyPlaced(true);
        }

        $quote = null;
        try {
            //reload quote; need to check is still active
            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
            if($quote->getId()) {
                $klarnaOrder->assignQuote($quote,$validate = false);
            }
        } catch(Exception $e) {
            $this->_logPush("Cannot load and assign to Klarna Order the quote {$order->getQuoteId()}");
            Mage::logException($e);
        }

        //inactivate quote is still active
        if($quote && $quote->getId()) {
            if($quote->getIsActive()) {
                //inactivate quote
                $quote->setIsActive(false);
                try {
                    $quote->save(); //need to save the quote (after the order is placed, the quote is inactivated)
                    $this->_logPush("Quote {$quote->getId()} was inactivated",$prefix);
                } catch(Exception $e) {
                    $this->_logPush("Cannot inactivate quote {$quote->getId()}, {$e->getMessage()}",$prefix);
                    Mage::log(__METHOD__."  Klarna reservation {$klarnaData['reservation']}, quote {$quote->getId()}; Order was created ({$order->getIncrementId()}) but cannot save the quote (mark as inactive), {$e->getMessage()}");
                    Mage::logException($e);
                }
            } else {
                $this->_logPush("Quote {$quote->getId()} already inactivated",$prefix);
            }
        } else {
            $this->_logPush("We do not have a quote?!?",$prefix);
        }


        if($klarnaData['status'] != 'created') {

            //update klarna status (to created) and register magento order id to klarna system
            try {
                $klarnaOrder->registerOrder($order->getIncrementId());
                $this->_logPush("Klarna status was update to created",$prefix);
                $pushRequest->setMarshal(serialize($klarnaOrder->marshal()))->save();
                $this->_logPush("Klarna data was updated in push request",$prefix);
            } catch(Exception $e) {
                $this->_logPush("Cannot register Klarna order / update klarna status to created, {$e->getMessage()}",$prefix);
                //just log exception, we will try later, via push action (also, we could activate reservation even the status is not created)
                Mage::log(__METHOD__." Klarna order {$kID}, reservation {$klarnaData['reservation']}, order is placed ({$order->getIncrementId()}) but status cannot be set to created, {$e->getMessage()}");
                Mage::logException($e);
            }
        } else {
            $this->_logPush("Klarna status is already 'created', do not set again",$prefix);
        }

        $order->setKlarnaOrder($klarnaOrder);
        return $order;
    }


    //place the order (if not) and redirect to thankyou page
    public function confirmationAction() {



        $kID   = $this->getRequest()->getParam('kid');
        $test  = (int)$this->getRequest()->getParam('test',-1);
        $store = (int)$this->getRequest()->getParam('store',-1);

        $prefix = "CONF ($kID,$test,$store)";
        $this->_logPush("START",$prefix);

        if(!$kID || $test<0 || $store<0) {
            $this->_logPush("INVALID (redirect user to cart)",$prefix);
            $this->_getSession()->addError($this->__('Invalid request'));
            $this->_redirect('checkout/cart');
            return;
        }


//         $this->_logPush("Wait 6 seconds...",$prefix);
//         sleep(6);
//         $this->_logPush("CONTINUE, trying to place order",$prefix);

        try {

            $order = $this->_placeOrder($kID,$test,$store,$prefix);
            if($order->getIsAlreadyPlaced()) {
                $this->_logPush("Found already placed order {$order->getIncrementId()}",$prefix);
            } else {
                $this->_logPush("Order {$order->getIncrementId()} was created",$prefix);
            }
        } catch(Exception $e) {

            $this->_getSession()->unsKlarnaOrderUri(); //remove Klarna Order

            $code = $e->getCode();
            $this->_logPush("FAIL, [{$code}] {$e->getMessage()}",$prefix);
            switch($code) {
                case self::ERR_KLARNA_FETCH: //cannot fetch Klarna order
                    echo $this->__('<h1 style="color:red">Cannot fetch the Klarna confirmation</h1><p>Please <a href="%s">refresh this page</a> or wait for confirmation (the order will be placed when we get the Klarna confirmation). Please contact us if you don\'t receive the order confirmation (by email).</p><p><br><em>If you</em> <a href="%s">go back</a><em>, a new order will be created (and the previous one will be canceled)</em></p>',Mage::getUrl('*/*/confirmation'),Mage::getUrl('*/*'));
                    exit("<p><br><br>{$e->getMessage()}</p>");
                case self::ERR_KLARNA_INVALID: //invalid response, missing status or reservation
                    $this->_getSession()->addError($this->__("The order was NOT placed, %s",$e->getMessage()));
                    $this->_getSession()->addNotice('<strong>'.$this->__("Please review the order and retry to place it.").'</strong>');
                    break;
                case self::ERR_KLARNA_STATUS: //not expected status (complete/created)
                    $this->_getSession()->addError($this->__('Please complete the checkout, the Klarna order is not complete.'));
                    break;
                case self::ERR_KLARNA_ORDER_CANCELED: //cannot place order, reservation was canceled
                    $this->_getSession()->addError($this->__('Cannot place the order, %s',$e->getMessage()));
                    $this->_getSession()->addNotice('<strong>'.$this->__("Please review the order and retry to place it, the previous order was CANCELED.").'</strong>');
                    break;
                default:
                    //order cannot be placed (and Klarna reservation, if any, was NOT cacneled)
                    $this->_getSession()->addError($this->__('Cannot place the order, %s',$e->getMessage()));
                    $this->_getSession()->addNotice('<strong>'.$this->__("Please review the order and retry to place it, the previous order will be canceled.").'</strong>');
                    break;
            }
            $this->_redirect('*/*');
            return;
        }

        //clear LastOrderId, LastRecurringProfileIds, RedirectUrl, LastBillingAgreementId etc.
        $this->_getSession()->clearHelperData();
        //clear checkout session
        $this->_getSession()->clear(); //this will not "unsetAll" (see Mage_Checkout_Model_Session::clear)


        $klarnaOrder = $order->getKlarnaOrder();

        $quote = $klarnaOrder->getQuote();

        // add order information to the session
        $this->_getSession()
            ->setLastSuccessQuoteId($order->getQuoteId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId())
        ;

         $customerId             = $order->getCustomerId();
         if($customerId) {

            //login customer, if not
            $customerSession        = $this->getKlarnaCheckout()->getCustomerSession();
            //using getData checkout_method because quote was own getCheckoutMethod and we want original (data) one
            $checkoutMethod         = $quote?$quote->getData('checkout_method'):'unknown';
            $isConfirmationRequired = (bool)Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_IS_CONFIRM, $order->getStore());

            $this->_logPush("Customer ID {$customerId}; Logged IN: ".($customerSession->isLoggedIn()?"YES":"NO")."; Checkout Method: {$checkoutMethod}; Confirmation required: ".($isConfirmationRequired?"YES":"NO"),$prefix);

            if(($checkoutMethod == NWT_KCO_Model_Checkout::METHOD_REGISTER) && !$customerSession->isLoggedIn() && !$isConfirmationRequired) {
                $this->_logPush("Login customer {$customerId}",$prefix);
                $customerSession->loginById($customerId);
            } else {
                $this->_logPush("Skip kogin customer",$prefix);
            }
         } else {
            $this->_logPush("No customer ID (guest user)",$prefix);
         }

        //set this in session, to avoid refetch klarna order (from klarna server), in thank you page
        $this->_getSession()->setKlarnaOrder($klarnaOrder->marshal());

        $this->_redirect('*/*/thankyou');
        $this->_logPush("END",$prefix);
    }


    public function pushAction() {

        $kID  = $this->getRequest()->getParam('kid');
        $test = (int)$this->getRequest()->getParam('test',-1);
        $store = (int)$this->getRequest()->getParam('store',-1);

        $prefix = "\tPUSH ($kID,$test,$store)";
        $this->_logPush("START",$prefix);

        if(!$kID || $test<0 || $store<0) {
            $this->_logPush("INVALID request",$prefix);
            return $this->_croak('Invalid request');
        }

        //check we already start process request (by confirmationAction)
        $pushRequest = Mage::getModel('nwtkco/push')->loadByKid($kID,$test,$store);
        if(!$pushRequest->getId()) {
            $this->_logPush("No already started process found, wait (max) 10 seconds",$prefix);
            for($i=0;$i<5;$i++) {
                sleep(2); //wait for confirmation action
                $pushRequest = Mage::getModel('nwtkco/push')->loadByKid($kID,$test,$store);
                if($pushRequest->getId()) {
                    $this->_logPush("Found just already started process (confirmation) #{$pushRequest->getId()}, do nothing (we will wait for other process to finish)",$prefix);
                    exit("The request is processed via confirmation action, we will wait to finish");
                    break;
                }
            }
        } else {
            $age  = $pushRequest->getAge(); //minutes
            if($age < 5) { //5 minutes
                $this->_logPush("Found already started process (confirmation), {$age} minutes old, do nothing (we will wait for other process to finish)",$prefix);
                exit("The request is processed via confirmation action, we will wait to finish");
            } else {
                $this->_logPush("Found already started process (confirmation), {$age} minutes old, check if we have order placed or error",$prefix);
            }
        }


        //recheck Klarna order
        try {
            $klarnaOrder = Mage::getModel('nwtkco/klarna_order')->setTestMode($test>0)->fetchForUpdate($kID,null);
            $this->_logPush("Klarna order found (fetched)",$prefix);
        } catch(Exception $e) {
            $this->_logPush("Cannot fetch Klarna order (was previous canceled?), {$e->getMessage()}",$prefix);
            return $this->_fail("Cannot fetch Klarna order {$kID}, {$e->getMessage()}");
        }

        $klarnaData = $klarnaOrder->marshal();
        //check klarna order status/reservation
        if(empty($klarnaData['status'])) {
            $this->_logPush($msg="Invalid Klarna response (missing status)",$prefix);
            return $this->_bad($msg);
        }
        if($klarnaData['status'] != 'checkout_complete') {
            if($klarnaData['status'] == 'created') {
                $this->_logPush($msg="Klarna order already 'created', do nothing!",$prefix);
            } else {
                $this->_logPush($msg="Invalid status {$klarnaData['status']}, expect checkout_complete",$prefix);
                return $this->_bad($msg);
            }
            exit($msg);
        }

        if(empty($klarnaData['reservation'])) {
            $this->_logPush($msg="Invalid Klarna response (missing reservation)",$prefix);
            return $this->_bad($msg);
        }




        //check if we have already placed order
        $order = Mage::getModel('sales/order')->load($klarnaData['reservation'],'nwt_reservation');

        if($order->getId()) {
            try {
                $klarnaOrder->registerOrder($order->getIncrementId());
                $this->_logPush($msg = "Found an already placed order, {$order->getIncremendId()} for {$klarnaData['reservation']} reservation; Order was registered to Klarna",$prefix);
            } catch(Exception $e) {
                $this->_logPush($msg = "Found an already placed order, {$order->getIncremendId()} for {$klarnaData['reservation']} reservation; FAIL to register order to Klarna, $e->getMessage()",$prefix);
                Mage::logException($e);
                return $this->_fail($msg);
            }
            exit($msg);
        }

       //no order placed, check if confirmation request was finished with error
       if($pushRequest->getId() && $pushRequest->getError()) {
            try {
                $klarnaOrder->cancelReservation();
                $this->_logPush($msg="Confirmation process was finished with error [{$pushRequest->getError()}], {$pushRequest->getErrorMsg()}; Reservation was canceled",$prefix);
            } catch(Exception $e) {
                $this->_logPush($msg="Confirmation process was finished with error [{$pushRequest->getError()}], {$pushRequest->getErrorMsg()}; Fail to cancel reservation, {$e->getMessage()}",$prefix);
                Mage::logException($e);
                return $this->_fail($msg);
            }
            exit($msg);
        }


        //the process was started but we don't have an order, also no error
        if($pushRequest->getId()) {
            //TODO: cancel the registration? (the process was started but... we don't have order also no error; )
            $this->_logPush("Found already started process (confirmation), {$age} minutes old, we will do not retry to place order, we will let reservation active (let owner to decide)",$prefix);
            exit("We already trying to place order but... we will not try again, let store owner to decide");
        }


        //try to place order; we assume that the user has never returned to confirmation action
        try {
            $order = $this->_placeOrder($kID,$test,$store,$prefix);
            if($order->getIsAlreadyPlaced()) {
                $this->_logPush("Found already placed order {$order->getIncrementId()}; do nothing.",$prefix);
            } else {
                $this->_logPush("Order {$order->getIncrementId()} was created",$prefix);
            }
        } catch(Exception $e) {
            $code = $e->getCode();
            $this->_logPush("Cannot register/place order for klarna order {$kID}, [$code] {$e->getMessage()}.",$prefix);
            Mage::log($msg = __METHOD__."Cannot register/place order for klarna order {$kID}, [$code] {$e->getMessage()} (see exception.log)");
            Mage::logException($e);
            exit($msg);
        }
        exit("Klarna Order {$kID}, Magento order {$order->getIncrementId()} was created. END");
    }



    /**
     * Order success (thankyou) action
     */
    public function thankyouAction()
    {

        $session = $this->_getSession();

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $title = Mage::helper('nwtkco')->getThankyouTitle();
        if(!$title) {
            $title = Mage::helper('nwtkco')->getTitle();
        }
        $this->getLayout()->getBlock('head')->setTitle($title?$title:$this->__('Klarna Checkout'));

        //Just LAYOUT test
        if($this->getRequest()->getParam('nwtkco')=='test') {
            $mid = $this->getRequest()->getParam('mid');
            $kid = $this->getRequest()->getParam('kid');
            if($mid && $kid) {
                $order = Mage::getModel('sales/order')->load($mid);
                if($order->getId() && $order->getNwtReservation() == $kid) {
                    $block = $this->getLayout()->getBlock('google_analytics');
                    if($block) {
                        $block->setOrderIds(array($mid));
                    }
                } else {
                    exit('Marhs...');
                }
            }
            $this->renderLayout();
            return;
        }


        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::register('KlarnaOrder',$this->_getSession()->getKlarnaOrder()); //this is need by thank you block; klarnaOrder is set in confirmationAction

        $this->_getSession()->clear();
        $this->_getSession()->unsKlarnaOrderUri(); //unset klarna location
        $this->_getSession()->unsKlarnaOrder(); //unset klarna location


        if( ($lastOrderId = $session->getLastOrderId()) ) {
            //no, is not onepage, do not use it (this is used by google analytics but we have on ga.phtml (see layout xml)
            //Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
            //dispatch own event
            Mage::dispatchEvent('checkout_nwtkco_controller_success_action', array('order_ids' => array($lastOrderId)));
            $block = $this->getLayout()->getBlock('google_analytics');
            if($block) {
                $block->setOrderIds(array($lastOrderId));
            }
        }
        $this->renderLayout();
    }
    
    public function validationAction() {

      $helper = Mage::helper('nwtkco');
      $minAge = $helper->getMinimumAge();

      if($minAge<=0) { //do nothing
	exit("OK");
      }
      
       
      $data = @file_get_contents("php://input");
     
      //$data = trim($data);
      if(!$data) {
	Mage::log("No data received",null,"kco-validation.log");
	$this->_bad("No data received");
      }
      
      
      $data = @json_decode($data,$assoc = true);
      if(!$data || !is_array($data)) {
      	Mage::log("Invalida data received, empty or not array",null,"kco-validation.log");
      	Mage::log($data,null,"kco-validation.log");
	$this->_bad("Invalid data");
      }
      
      $url = $helper->getCheckoutUrl('error');
      
      if(empty($data['customer']) || empty($data['customer']['date_of_birth'])) {

	Mage::log("Invalida data received, missing customer[date_of_birth]",null,"kco-validation.log");
      	Mage::log($data,null,"kco-validation.log");

	$err = $helper->__("Missing date of birth, please review your information (you need to be at least %s years old)",$minAge);
	$url .= "?err=".base64_encode($err);
	header('Location: ' . $url, true, 302);
	exit($err);
      }


      
      $dob = $data['customer']['date_of_birth'];

      $dobObject = new DateTime($dob);
      $nowObject = new DateTime();
      
      $year = $dobObject->format('Y');
      
      if($year <= 1901 || $year > 2100) { //I hope nobody will use this after 2100

	Mage::log("Invalid DOB [{$dob}] received",null,"kco-validation.log");
	Mage::log($dobObject,null,"kco-validation.log");
	Mage::log($data,null,"kco-validation.log");
	$err = $helper->__("Invalid date of birth [%s], please review your information (you need to be at least %s years old)",$dob,$minAge);
	$url .= "?err=".base64_encode($err);
	header('Location: ' . $url, true, 302);
	exit($err);    
      }

      $age = $dobObject->diff($nowObject)->y;
      
    
      if($age<=0 || $age< $minAge) {
	  Mage::log("Invalid age [{$age}] received (DOB: [{$dob}]), minimum [{$minAge}] years is required, redirect customer to checkout",null,"kco-validation.log");
	  $err = $helper->__("You need to be at least %s years old (your date of birth: [%s], age: [%s])",$minAge,$dob,$age);
	  $url .= "?err=".base64_encode($err);
	  header('Location: ' . $url, true, 302);
	  exit($err);
      }
      
      Mage::log($data,null,"kco-validation.log");
      Mage::log("DOB: {$dob}; AGE: {$age}; MinimumAge: {$minAge}, customer allowed");
      
      exit("OK");
    }
    
    public function errorAction() {
      $err = '';
      if(isset($_GET['err'])) {
	$err = (string)$_GET['err'];
	$err = @base64_decode($err);
      }
      if($err) {
	$this->_getSession()->addError($err);
      }
      $this->_redirect('*/*');
    }


    protected function _fail($msg = 'Marsh!')   { header('HTTP/1.1 503 Service Temporarily Unavailable',true, 503); exit($msg); }
    protected function _bad($msg = 'Marsh!')    { header('HTTP/1.1 400 Bad Request', true, 400); exit($msg); }
    protected function _croak($msg = 'Marsh!')  { header('HTTP/1.1 403 Forbidden',true,403); exit($msg); }



    protected function _logPush($msg,$prefix=null) {
        if($prefix) {
            $msg = "[{$prefix}] $msg";
        }
        Mage::log($msg,null,'nwtkco-push.log');
        return $this;
    }




}
