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
 * Cart Item Configure block
 * Updates url for upateItemOptions
 *
 */

class NWT_KCO_Block_Cart_Item_Configure extends Mage_Core_Block_Abstract
{

    /**
     * Configure product view blocks
     *
     * @return Mage_Checkout_Block_Cart_Item_Configure
     */
    protected function _prepareLayout()
    {
        if( (string)$this->getRequest()->getParam('from') == 'klarnakassan') {
            // Set custom submit url route for form - to submit updated options to cart
            $block = $this->getLayout()->getBlock('product.info');
            if ($block) {
                $block->setSubmitRouteData(array(
                    'route' => 'checkout/cart/updateItemOptions',
                    'params' => array('id' => $this->getRequest()->getParam('id'),'from'=>'klarnakassan')
                ));
            }
            $block = $this->getLayout()->getBlock('product.info.addto');
            if($block) {
                $block->setTemplate('nwt/kco/product/addto.phtml');
            }

        }
        return parent::_prepareLayout();
    }
}
