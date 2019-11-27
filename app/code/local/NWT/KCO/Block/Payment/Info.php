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
 * Payment information block
 */
class NWT_KCO_Block_Payment_Info extends Mage_Payment_Block_Info
{


    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('nwt/kco/payment/info.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('nwt/kco/payment/info.pdf.phtml');
        return $this->toHtml();
    }
}
