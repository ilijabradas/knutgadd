<?php

class PI_Oopsprofile_Helper_Mail extends Mage_Core_Helper_Abstract {

    protected function _saveAttachment($attachment, $path, $file) {

        //try{

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        if ($attachment['encoding'] == 'base64') {
            $fileContent = base64_decode($attachment['content']);
        } else {
            $fileContent = $attachment['content'];
        }

        $io->streamWrite($fileContent);
        $io->close();
        //}catch(Exception $e){
        //}

        return $this;
    }

    public function getFileNameWithPrefix($attachmentName) {

        $storeId = Mage::app()->getStore()->getId();
        //Get the timestamp
        $filePrefix = 'oops_' . Mage::app()->getLocale()->storeTimeStamp($storeId);

        return $filePrefix . '_' . $attachmentName;
    }

    /*
     * Save attachments to var/attachments
     */

    public function _saveAllAttachments($allAttachments, $path) {


        $files = array();
        if (substr($path, -1) != DS) {
            $path .= DS;
        }

        //$path = Mage::getBaseDir('var') . DS . 'import' . DS . 'attachments' . DS;

        foreach ($allAttachments as $msgId => $attachments) {
            foreach ($attachments as $attachment) {
                $filename = $this->getFileNameWithPrefix($attachment['filename']);
                $this->_saveAttachment($attachment, $path, $filename);
                $files[] = $filename;
            }
        }

        return $files;
    }

}
