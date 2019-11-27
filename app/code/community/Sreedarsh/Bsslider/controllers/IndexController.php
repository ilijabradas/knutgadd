<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Front end entry point
     * Always redirects to the startup page url
     */
     public function indexAction()
        {
            $this->getLayout();
            $this->renderLayout();
        }

}