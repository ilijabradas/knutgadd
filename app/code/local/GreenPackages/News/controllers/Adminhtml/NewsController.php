<?php

class GreenPackages_News_Adminhtml_NewsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('news/newss')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Newss Manager'), Mage::helper('adminhtml')->__('News Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
/*---------------edit existing news content----------*/
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('news/news')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('news_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('news/newss');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('News Manager'), Mage::helper('adminhtml')->__('News Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('News News'), Mage::helper('adminhtml')->__('News News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('news/adminhtml_news_edit'))
				->_addLeft($this->getLayout()->createBlock('news/adminhtml_news_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('News does not exist'));
			$this->_redirect('*/*/');
		}
	}
/*---------------add new news content----------*/ 
	public function newAction() {
		$this->_forward('edit');
	}
/*------------save new / old news content----------*/ 
	public function saveAction() {
				
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('news/news');		
			if(isset($_FILES['image_one']['name']) && $_FILES['image_one']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image_one');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . '/'  .'news' . '/' . 'images' .'/' ;
					
					$uploader->save($path, $_FILES['image_one']['name'] );
					$pathUrl = Mage::getBaseUrl('media') . '/'  .'news' . '/' . 'images' .'/' ;
					$uploadFileName = $uploader->getUploadedFileName();
	  				$data['image_one'] = $pathUrl.$uploadFileName;
					$model->setFilename($data['image_one']);
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
				
			}
			if(is_array($data['image_one']))
			{
				if($data['image_one']['delete'])
				{
					$data['image_one'] = "";
				}
				else{
					$data['image_one'] = $data['image_one']['value'];
				}
				$model->setFilename($data['image_one']);
			}

			if(isset($_FILES['image_two']['name']) && $_FILES['image_two']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image_two');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . '/'  .'news' . '/' . 'images' .'/' ;
					
					$uploader->save($path, $_FILES['image_two']['name'] );
					$pathUrl = Mage::getBaseUrl('media') . '/'  .'news' . '/' . 'images' .'/' ;
					$uploadFileName = $uploader->getUploadedFileName();
	  				$data['image_two'] = $pathUrl.$uploadFileName;
					$model->setFilename($data['image_two']);
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
				
			}
			if(is_array($data['image_two']))
			{
				if($data['image_two']['delete'])
				{
					$data['image_two'] = "";
				}
				else{
					$data['image_two'] = $data['image_two']['value'];
				}
				$model->setFilename($data['image_two']);
			}
	  			
	  			
					
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
			
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('news')->__('News was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Unable to find news to save'));
        $this->_redirect('*/*/');
	}
/*-------------------delete specific news---------*/ 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('news/news');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('News was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
/*-------------------delete all selected news---------*/ 
    public function massDeleteAction() {
       
		$newsIds = $this->getRequest()->getParam('news');
        if(!is_array($newsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select news(s)'));
        } else {
            try {
                foreach ($newsIds as $newsId) {
                    $news = Mage::getModel('news/news')->load($newsId);
                    $news->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($newsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
		
    
        $this->_redirect('*/*/index');
	}	
/*-------------------change status of all selected news---------*/ 	
    public function massStatusAction()
    {
        $newsIds = $this->getRequest()->getParam('news');
		
		Mage::log('status change');
		Mage::log($newsIds);
		
		
		if(!is_array($newsIds)) {
            Mage::getModel('adminhtml/session')->addError($this->__('Please select news(s)'));
        } else {
            try {
                
				foreach ($newsIds as $newsId) {
                    $news = Mage::getSingleton('news/news')
                        ->load($newsId)
                        ->setIsMassupdate(true)
                        ->save();
                }
				
				
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($newsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'news.csv';
        $content    = $this->getLayout()->createBlock('news/adminhtml_news_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'news.xml';
        $content    = $this->getLayout()->createBlock('news/adminhtml_news_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
