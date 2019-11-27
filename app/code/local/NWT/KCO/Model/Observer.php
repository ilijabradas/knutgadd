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
 * Klarna Checkout Observer
 */
class NWT_KCO_Model_Observer {

    /**
     * Redirect to Klarnakassan after cart item was updated
     *
     * @event   controller_action_postdispatch_checkout_cart_updateItemOptions
     *
     * @param   Varien_Event_Observer $observer
     * @return  NWT_KCO_Model_Observer
     */
    public function redirectToKlarnakassan(Varien_Event_Observer $observer)
    {

        $action = $observer->getEvent()->getControllerAction(); /* Mage_Core_Controller_Varien_Action */
        if($action &&  (string)$action->getRequest()->getParam('from') == 'klarnakassan') {
            $action->getResponse()->setRedirect(Mage::helper('nwtkco')->getCheckoutUrl());
        }
        return $this;
    }


    //@event sales_quote_collect_totals_after
    //@event checkout_cart_save_after

    public function markCartDirty(Varien_Event_Observer $observer) {
        $checkout = Mage::helper('nwtkco')->getKlarnaCheckout();
        if($checkout->getDoNotMarkCartDirty()) {
            //this is used in thank you page, we do not want that in checkout page to reload the page while we are still in thank you page
            //and wait to redirect
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        Mage::getSingleton('core/cookie')->set("NwtKCOCartCtrlKey", Mage::helper('nwtkco')->getKlarnaCheckout()->getQuoteSignature($quote), $period = null, $path = null, $domain = null, $secure = null, $httponly = false);
    }

}

