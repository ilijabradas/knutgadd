<?php
class GreenPackages_News_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home',
                array('label'=>Mage::helper('news')->__('Home'), 'title'=>Mage::helper('news')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl())
            );
            $breadcrumbsBlock->addCrumb('listado',
                array('label'=>Mage::helper('news')->__('News & Innovations'), 'title'=>Mage::helper('news')->__('Go to News & Innovations'), 'link'=>$this->getUrl('news/listado'))
            );


        }
        return parent::_prepareLayout();
    }

}
