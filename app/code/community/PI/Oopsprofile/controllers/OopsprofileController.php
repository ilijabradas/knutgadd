<?php

class PI_Oopsprofile_OopsprofileController extends Mage_Core_Controller_Front_Action {

    protected function readProfileFromFile() {

        $path = Mage::getBaseDir('var') . DS . 'gmail';
        $file = 'oops_profile_mail_client.tmp';

        $io = new Varien_Io_File();
        $io->open(array('path' => $path));
        $content = $io->read($file);
        $io->rm($file); //remove file
        $io->close();

        return unserialize($content);
    }

    /*
     * Save Token for the oopsprofile
     */

    public function authenticateNsaveTokenAction() {

        Mage::getSingleton('adminhtml/session')->unsAuthenticationUrl();
        if ($code = Mage::app()->getRequest()->getParam('code')) {

						$config = $this->readProfileFromFile();
            if (!$config) {

                return Mage::getSingleton('core/session')->addError('unable to read the profile.');
            } else {

                $tokenFile = 'oops_' . $config['profile_id'] . '_' . 'mail.tmp';
                $tokenPath = Mage::getBaseDir('var') . DS . 'token';

                try {

                    $client = Mage::getSingleton('oopsprofile/mail_google_client');
                    $client->setClientId($config['email']['gmail']['client_id']);
                    $client->setClientSecret($config['email']['gmail']['client_secret']);
                    $client->setRedirectUri($config['email']['gmail']['redirect_uri']);
                    $client->setDeveloperKey($config['email']['gmail']['developer_key']);

                    $client->setScopes("https://mail.google.com/");

                    $client->authenticate($code);

                    if ($token = $client->getAccessToken()) {

                        $io = new Varien_Io_File();
                        $io->setAllowCreateFolders(true);
                        $io->open(array('path' => $tokenPath));
                        $io->streamOpen($tokenFile, 'w');
                        $io->streamLock(true);
                        $io->streamWrite($token);
                        $io->close();

												//set Access Token has been generated to the table serialize and save it to DB
												$oopsProfile = Mage::getModel('oopsprofile/oopsprofile')->loadByProfile($config['profile_id']);
												$oopsProfile->setIsTokenAvailable(1)->save();

                        $this->loadLayout();

                        Mage::getSingleton('core/session')->setAccessTokenGmail($token);
                        Mage::getSingleton('core/session')->addSuccess(Mage::helper('oopsprofile')->__('Access token has been saved.'));
                    } else {
                        Mage::getSingleton('core/session')->addError(Mage::helper('oopsprofile')->__('Problem detected saving access token.'));
                    }
                } catch (Exception $e) {
                    //print_r($e);
                    Mage::log($e);
                }
            }
        }

        $this->renderLayout();
    }

    /*public function testAction()
    {
				$processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
				$processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL));
				$processes->walk('save');
    }*/
    

}
