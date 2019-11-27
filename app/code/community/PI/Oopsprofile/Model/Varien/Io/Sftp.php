<?php
class PI_Oopsprofile_Model_Varien_Io_Sftp extends Varien_Io_Sftp
{

		/**
     * Write a file
     * @param $src Must be a local file path with name
     */     
		public function write($filename, $src, $mode=null)
    {
        return $this->_connection->put($filename, $src,NET_SFTP_LOCAL_FILE);//"NET_SFTP_LOCAL_FILE" use to save from local file
    }

    
}
