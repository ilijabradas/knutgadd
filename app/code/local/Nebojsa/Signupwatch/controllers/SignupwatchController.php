<?php

class Nebojsa_Signupwatch_SignupwatchController extends Mage_Core_Controller_Front_Action {

	public function saveAction() {

		$model = Mage::getModel('signupwatch/signupwatch');
		$data = $this->getRequest()->getPost();
		if (empty($_POST['country_code'])) {
			return;
		} else {
			try {
				$watch_name = str_replace('/', '', $data['watch_name']);
				$model->setData($data)->save();
				Mage::getSingleton('core/session')->addSuccess($this->__("You have successfully signed Up for the " . $watch_name . " watch."));
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e);
			}

			$this->_redirectReferer();
		}

	}

}
