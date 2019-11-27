<?php
class Webinerds_TemplateDevelopment_Model_Import
{
    /**
     * Import Function
     * @param $data
     * @param $storeId
     * @param bool $isPage
     */
    public function saveCmsData($data, $storeId, $isPage = false) {
        if ($isPage) {
            $model = Mage::getModel('cms/page');
        } else {
            $model = Mage::getModel('cms/block');
        }
        $collection = $model->getCollection()
            ->addFieldToFilter('identifier', $data['identifier'])
            ->addStoreFilter($storeId);
        $cmsItem = $collection->getFirstItem();
        if ($cmsItem && ($cmsItem->getBlockId()||$cmsItem->getPageId())) {
            $oldData = $cmsItem->getData();
            $data = array_merge($oldData, $data);
            $cmsItem->setData($data)->save();
        } else {
            $model->setData($data)->save();
        }
        return;
    }
}