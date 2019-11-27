<?php
/**
 * NWT_Newrelicfix Notice
 *
 * @package     NWT_Newrelicfix (Newrelic Fix)
 * @author      Fredrik Blanco fredrik@nordicwebteam.se
 * @copyright   Copyright (c) 2014 Nordic Web Team
 * 
 */

require_once 'Mage/Adminhtml/controllers/System/Email/TemplateController.php';


class NWT_Newrelicfix_Adminhtml_System_Email_TemplateController extends Mage_Adminhtml_System_Email_TemplateController
{
    

    /**
     * Lets disable New Relic here
     *
     */

    public function defaultTemplateAction()
    {
        if (extension_loaded('newrelic')) {
            newrelic_disable_autorum();
        }
   
        parent::defaultTemplateAction();

       

    }


}
