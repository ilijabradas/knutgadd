<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Adminhtml_BssliderController extends Mage_Adminhtml_Controller_Action {

    protected function _initBsslider($idFieldName = 'id')
    {
        $imageId = (int) $this->getRequest()
                        ->getParam($idFieldName);
        if ($imageId) {
            $bsslider = Mage::getModel('bsslider/bsslider')
                    ->load($imageId);
        }

        Mage::register('current_bsslider', $bsslider);

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Bootstrap Slider'))->_title($this->__('Manage Slider'));
        $this->loadLayout();
        $this->_setActiveMenu('bsslider');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_title($this->__('Bootstrap Slider'))->_title($this->__('New slider image'));

        $this->loadLayout();

        $this->_setActiveMenu('bsslider');

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Bootstrap Slider'))->_title($this->__('Edit Slider'));

        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('bsslider/bsslider');
        if ($id) {
            $model->load((int) $id);

            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bsslider')->__('Image does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('bsslider_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('bsslider');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()
                        ->createBlock('bsslider/adminhtml_bsslider_grid')
                        ->toHtml()
        );
    }

    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();



        if ($post_data) {

            try {

                try {

                    if ((bool) $post_data['image_name']['delete'] == 1) {
                        unlink(Mage::getBaseDir('media') . DS . $post_data['image_name']['value']);
                        $post_data['image_name'] = '';
                    } else {

                        unset($post_data['image_name']);

                        if (isset($_FILES)) {

                            if ($_FILES['image_name']['name']) {

                                if ($this->getRequest()->getParam("id")) {
                                    $model = Mage::getModel("bsslider/bsslider")->load($this->getRequest()->getParam("id"));
                                    if ($model->getData('image_name')) {
                                        $io = new Varien_Io_File();
                                        $io->rm(Mage::getBaseDir('media') . DS . implode(DS, explode('/', $model->getData('image_name'))));
                                    }
                                }
                                //upload image

                                $path = Mage::getBaseDir('media') . DS . 'bsslider' . DS . 'slides' . DS;
                                $uploader = new Varien_File_Uploader('image_name');
                                $uploader->setAllowRenameFiles(false);
                                $uploader->setAllowedExtensions(array('jpg', 'png', 'gif'));

                                $uploader->setFilesDispersion(false);
                                $destFile = $path . $_FILES['image_name']['name'];
                                $filename = $uploader->getNewFileName($destFile);
                                $uploader->save($path, $filename);

                                $post_data['image_name'] = 'bsslider/slides/' . $filename;
                            }
                        }
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                $model = Mage::getModel('bsslider/bsslider')
                        ->addData($post_data)
                        ->setId($this->getRequest()->getParam('id'))
                        ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Image was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setSliderData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setSliderData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction()
    {
        $this->_initBsslider();
        $bsslider = Mage::registry('current_bsslider');       

        if ($bsslider->getId()) {
            try {               
                $bsslider->delete();
                unlink(Mage::getBaseDir('media') . DS . $bsslider->getImageName());
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bsslider')->__('The slider image has been deleted.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $Ids = $this->getRequest()->getParam('bsslider');

        if (!is_array($Ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bsslider')->__('Please select image(s) to delete.'));
        } else {
            try {
                $bsslider = Mage::getModel('bsslider/bsslider');
                foreach ($Ids as $Id) {
                    $bsslider
                            ->reset()
                            ->load($Id)
                            ->delete();

                    unlink(Mage::getBaseDir('media') . DS . $bsslider->load($Id)->getImageName());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('bsslider')->__('Total of %d record(s) were deleted.', count($Ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

}