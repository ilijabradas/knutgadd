<?php 
class Vsourz_Imagegallery_Adminhtml_ImageController extends Mage_Adminhtml_Controller_action {
	protected function _initAction(){
		$this->loadLayout()->_setActiveMenu('imagegallery/imagedetail')->_addBreadcrumb(
			Mage::helper('adminhtml')->__('Manage Images'),
			Mage::helper('adminhtml')->__('Image Manager')
		);
		return $this;
	}
	public function indexAction(){
		$this->_initAction()->renderLayout();
	}
	public function newAction(){
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('imagegallery/adminhtml_image_edit'))->_addLeft($this->getLayout()->createBlock('imagegallery/adminhtml_image_edit_tabs'));
		$this->renderLayout();
	}
	public function saveAction(){
		 if ($data = $this->getRequest()->getPost()){
			$model = Mage::getModel('imagegallery/imagedetail');
			$id = $this->getRequest()->getParam('id');
			foreach ($data as $key => $value){
				if (is_array($value)){
					$data[$key] = implode(',',$this->getRequest()->getParam($key));
				}
			}
			if($id){
				$model->load($id);
			}
			//Code to Save Gallery Image
			if(isset($_FILES['gallery_img']['name']) && (file_exists($_FILES['gallery_img']['tmp_name']))){
				try{
					$uploader = new Varien_File_Uploader('gallery_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(false);
					// setAllowRenameFiles(true) -> move your file in a folder the magento way
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').'/imagegallery/';
					$imgName = explode('.',$_FILES['gallery_img']['name']);
					$imgName[0] = $imgName[0].'-'.'gallery-img'.'-'.date('Y-m-d H-i-s');
					$imgName = implode('.',$imgName);
					$imgName = preg_replace('/\s+/', '-', $imgName);
					$uploader->save($path, $imgName);
					$data['gallery_img'] = 'imagegallery/'.$imgName;
				}catch(Exception $e){
					
				}
			}
			else{       
				if(isset($data['gallery_img']) && $data['gallery_img']['delete'] == 1){
					// delete image file
					$image = explode(',',$data['gallery_img']);
					$img = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/'.$image[1];
					if(file_exists($img)){
						unlink($img);
					}
					// set db blank entry
					$data['gallery_img'] = ''; 
				}else{
					unset($data['gallery_img']);
				}
			}
			$model->setData($data);
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			try{
				if ($id){
					$model->setId($id);
				}
				$model->save();
				if (!$model->getId()){
					Mage::throwException(Mage::helper('imagegallery')->__('Error saving slide details'));
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('imagegallery')->__('Details was successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				}else{
					$this->_redirect('*/*/');
				}
			}catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				if ($model && $model->getId()) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				} else {
					$this->_redirect('*/*/');
				} 
			}
			return;
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('imagegallery')->__('No data found to save'));
		$this->_redirect('*/*/'); 
			
	}
	public function editAction(){
		$id = $this->getRequest()->getParam('id', null);
		$model = Mage::getModel('imagegallery/imagedetail');
		if($id){
			$model->load((int)$id);
			if($model->getId()){
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if($data){
				$model->setData($data)->setId($id);
			}
			}else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('imagegallery')->__('image does not exist'));
				$this->_redirect('*/*/');
			}
		}
		Mage::register('image_data', $model);
		$this->_title($this->__('Image Gallery'))->_title($this->__('Edit Image'));
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('imagegallery/adminhtml_image_edit'))->_addLeft($this->getLayout()->createBlock('imagegallery/adminhtml_image_edit_tabs'));
		$this->renderLayout(); 
	}
	public function deleteAction(){
		if ($this->getRequest()->getParam('id') > 0) {
			try{
				$model = Mage::getModel('imagegallery/imagedetail');
				$id = $this->getRequest()->getParam('id');
				$objModel = $model->load($id);
				$path = Mage::getBaseDir('media');
				unlink($path.'/'.$objModel->galleryImg);
				$model->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			}catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/'); 
	}
	public function massDeleteAction(){
		// Here the id is got from the function _prepareMassAction in Grid.php. ($this->getMassactionBlock()->setFormFieldName('id');)
		$ids = $this->getRequest()->getParam('id');
		if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('imagegallery')->__('Please select slide(s).'));
		}else{
			try{
				$imageModel = Mage::getModel('imagegallery/imagedetail');
				foreach($ids as $id){
					$objModel = $imageModel->load($id);
					$path = Mage::getBaseDir('media');
					unlink($path.'/'.$objModel->galleryImg);
					$objModel->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('imagegallery')->__('Total of %d record(s) were deleted.', count($ids)));
			}catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
}
?>