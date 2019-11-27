<?php

class Nebojsa_Signupwatch_Adminhtml_SignupwatchController extends Mage_Adminhtml_Controller_action
{

    public function indexAction()
    {
        $this->_initAction()
        ->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
        ->_setActiveMenu('signupwatch/signupwatchs')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Signupwatchs Manager'), Mage::helper('adminhtml')->__('Signupwatch Manager'));

        return $this;
    }

    public function exportCsvAction()
    {
        $fileName = 'signupwatch.csv';
        $content = $this->getLayout()->createBlock('signupwatch/adminhtml_signupwatch_grid')->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'signupwatch.xml';
        $content = $this->getLayout()->createBlock('signupwatch/adminhtml_signupwatch_grid')->getXml();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
}
