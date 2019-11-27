<?php

class PI_Oopsprofile_Model_Convert_Adapter_Mail extends Mage_Dataflow_Model_Convert_Adapter_Io {
    const DEFAULT_PROTOCOL = 'IMAP';
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_IMAP_PORT = 143;
    const IMAP_SSL_PORT = 993;

    const GOOGLE_IMAP_SERVICE = 'imap.gmail.com';
    protected $_encryptionTypes = array(
        'TLS',
        'SSL',
    );

    const DEFAULT_ATTACHMENT_SUBJECT = 'attachment';
    const DEFAULT_ATTACHMENT_SAVE_PATH = 'var/import/attachments';

    protected function readAccessTokenByService($service, $profileId) {

        $tokenName = 'oops_' . $profileId . '_mail.tmp';
        $tokenPath = Mage::getBaseDir('var') . DS . 'token';

        $io = new Varien_Io_File();
        $io->open(array('path' => $tokenPath));
        $content = $io->read($tokenName);
        $io->close();
        return json_decode($content, true);
    }

    protected function _constructAuthString($username, $token) {

        $accessToken = null;
        if (false === strpos($username, '@gmail.com')) {
            $email = $username . '@gmail.com';
        } else {
            $email = $username;
        }

        if (isset($token['access_token'])) {
            $accessToken = $token['access_token'];
        }
        if ($accessToken) {
            return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
        }
        return false;
    }

    protected function _sendAuthenticationRequest($imap, $username, $tokenData) {

        if ($authString = $this->_constructAuthString($username, $tokenData)) {
            $authenticateParams = array('XOAUTH2', $authString);
            $imap->sendRequest('AUTHENTICATE', $authenticateParams); //send request
            while (true) {
                $response = "";
                $is_plus = $imap->readLine($response, '+', true);
                if ($is_plus) {
                    $errorMsg = sprintf("got an extra server challenge: %s", $response);
                    Mage::log($errorMsg);
                    // Send empty client response.
                    $imap->sendRequest('');
                } else {
                    if (preg_match('/^NO /i', $response) ||
                            preg_match('/^BAD /i', $response)) {
                        //error_log("got failure response: $response");
                        $errorMsg = sprintf("got failure response: %s", $response);
                        Mage::log($errorMsg);
                        return false;
                    } else if (preg_match("/^OK /i", $response)) {
                        return true;
                    } else {
                        Mage::log('Some untagged response, such as CAPABILITY');
                    }
                }
            }
        }
    }
    
    protected function _buildProtocolConfig(&$config, $vars){
        
        $config['encryption'] = false; //overrided in case of a service provider like 'Gmail'
        
        if (isset($vars['host'])) {
            $config['host'] = $vars['host'];
        }

        if (isset($vars['port'])) {
            $config['port'] = $vars['port'];
        }

        if (isset($vars['encryption'])) {
            $config['ssl'] = $vars['encryption'];
        }

        if (isset($vars['username'])) {
            $config['user'] = $vars['username'];
        }

        if (isset($vars['password'])) {
            $config['password'] = $vars['password'];
        }

        if (isset($vars['service_provider']) && $vars['service_provider'] == 'gmail') {
            $config['encryption'] = true;
        }
    }

    /*
     * Return the storage object
     */

    public function getMailResource() {

        $mailStorage = null;
        $protocolConfig = array();
        
        $protocol = strtoupper($this->getVar('protocol', self::DEFAULT_PROTOCOL));
        
        $host = $this->getVar('host', self::DEFAULT_HOST);
        $port = $this->getVar('port');
        $pswd = $this->getVar('password');
        $username = $this->getVar('username');
        $encryption = $this->getVar('encryption');
        $serviceProvider = $this->getVar('service_provider', null);
        
        $currentProfile = Mage::app()->getRequest()->getParam('id');

        //updates the $protocolConfig by reference
        $this->_buildProtocolConfig($protocolConfig, $this->getVars());

        switch ($protocol) {
            case 'POP3'://not implemented 
            default:
                if ($serviceProvider) {
                    switch ($serviceProvider) {
                        case 'gmail':
                            //connect google imap service args => array('host'=> value,'port'=> value,'ssl'=> true)
														$imap = Mage::getModel('oopsprofile/mail_protocol_imap', $protocolConfig);
                            
                            /* read the current profile access token
                             * if not available display message to generate one using allow acess
                             */
                            $tokenData = $this->readAccessTokenByService($serviceProvider, $currentProfile);
                            //validate token with gmail
                            $isTokenOk = $this->_sendAuthenticationRequest($imap, $username, $tokenData);
                            if(true === $isTokenOk){
                                $mailStorage = Mage::getModel('oopsprofile/mail_storage_imap', $imap);
                            }
                            
                    }
                }else{
                    
                    //Attempt to open imap connection using array('host', 'port', 'username', 'password', 'ssl')
										//print_r($protocolConfig);exit;
										$mailStorage = Mage::getModel('oopsprofile/mail_storage_imap', $protocolConfig);
										
                    
                }
                
          }

        return $mailStorage;
    }

    /*
     * Fetch Attachment
     */

    public function load() {

        $savedAttachments = array();

        if (!$mailStorage = $this->getMailResource()) {
            $message = Mage::helper('oopsprofile')->__('Could not connect to the Mail server.');
            Mage::throwException($message);
        }

        $attachmentSubject = $this->getVar('mailsubject', self::DEFAULT_ATTACHMENT_SUBJECT);
        $attachmentSavePath = $this->getVar('attachmentsavepath', self::DEFAULT_ATTACHMENT_SAVE_PATH);
        $checkOnlyNewFlag = $this->getVar('checkonlynewmsgs', false); //only scan msgs with RECENT FLAG
        $deleteAttachmentFlag = $this->getVar('deleteattachment', false); //delete mails after fetching the attachments
        //try{
        //Attachments will be saved locally as per given subject to the path provided
        $savedAttachments = $mailStorage->fetchNSaveAttachmentsToImport(
                $attachmentSubject, $attachmentSavePath, $checkOnlyNewFlag, $deleteAttachmentFlag
        );
        //}catch(Exception $e){
        //	Mage::log($e);
        //}

        /*
         * Set vars to $this as if they were defined in the xml 
         */
        //echo 'helooo';exit;
        //print_r($savedAttachments);exit;

        if (!empty($savedAttachments)) {
            foreach ($savedAttachments as $file) {
                $this->setVar('filename', $file);
                $this->setVar('path', $attachmentSavePath);
                $this->setVar('type', 'file');
            }
        } else {
            $message = Mage::helper('oopsprofile')->__('Failed to fetch attachments from the mail server OR No attachments found on server.');
            Mage::throwException($message);
        }

        if (!$this->getResource()) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');
        $destFile = $batchModel->getIoAdapter()->getFile(true);


        $result = $this->getResource()->read($this->getVar('filename'), $destFile);
        $filename = $this->getResource()->pwd() . '/' . $this->getVar('filename');
        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not load file: "%s".', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Loaded successfully: "%s".', $filename);
            $this->addException($message);
        }

        $this->setData($result);
        return $this;
    }

    /**
     * Save result to destination file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save() {

        $helper = Mage::helper('oopsprofile/mail');

        $attachmentSubject = $this->getVar('mailsubject', self::DEFAULT_ATTACHMENT_SUBJECT);
        $attachmentSavePath = $this->getVar('attachmentsavepath', self::DEFAULT_ATTACHMENT_SAVE_PATH);
        $checkOnlyNewFlag = $this->getVar('checkonlynewmsgs', false); //only scan msgs with RECENT FLAG
        $deleteAttachmentFlag = $this->getVar('deleteattachment', false); //delete mails after fetching the attachments

        $this->setVar('path', $attachmentSavePath);


        if (!$this->getResource(true)) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);

        $filename = $this->getVar('filename');

        $result = $this->getResource()->write($filename, $dataFile, 0777);

        //attach the file as attachment and email it
        $mail = Mage::getModel('oopsprofile/mail');
        $mail->sendMailWithAttachment($this->getVars());

        if ($result === true) {
            
        }

        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
            if ($this->getVar('link')) {
                $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
            }
            $this->addException($message);
        }
        return $this;
    }

}
