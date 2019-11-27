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
 * (top) Links Block
 * add Klarna checkout link, remove others
 */
class NWT_KCO_Block_Links extends Mage_Core_Block_Template
{


    /**
     * Add Klarna Checkout link to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addKlarnakassanLink()
    {

        $helper = Mage::helper('nwtkco');

        $parentBlock = $this->getParentBlock();
        if(!$parentBlock || !$helper->addKlarnakassanLink()) { //if not addKlarnakassanLink, we will also not remove the others; addKlarnakassanLink will test also ifEnabled
            return $this;
        }

        if($helper->removeCheckoutLinks() ) {

            $links = array(
                Mage::getUrl('checkout'),
                Mage::getUrl('checkout/onepage'),
                Mage::helper('checkout/url')->getCheckoutUrl(),
            );

            //additional links
            $configLinks = $helper->getCheckoutLinks();

            foreach($configLinks as $link) {
                if($link && strpos('ttp:',$link)) {
                    $links[] = $link;
                } else {
                    $link = Mage::getUrl(ltrim($link,'/'));
                    if(!in_array($link,$links)) {
                        $links[] = $link;
                    }
                }
            }
            foreach($links as $link) {
                $parentBlock->removeLinkByUrl($link);
            }
        }

        
        //addKlarnakassanLink
        $text = $helper->getKlarnakassanLabel();
            $parentBlock->addLink(
                $text, $helper->getCheckoutPath(), $text,
                true, array('_secure' => true), 60, null,
                    'class="top-link-checkout"'
        );
        
        return $this;
    }


}
