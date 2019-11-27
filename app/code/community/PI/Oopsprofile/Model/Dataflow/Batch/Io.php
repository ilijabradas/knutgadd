<?php
class PI_Oopsprofile_Model_Dataflow_Batch_Io extends Mage_Dataflow_Model_Batch_Io
{
		public function openMulty($id,$write = true)
    {	
        $mode = $write ? 'w+' : 'r+';
        $ioConfig = array(
            'path' => $this->getPath()
        );
        $this->getIoAdapter()->setAllowCreateFolders(true);
        $this->getIoAdapter()->open($ioConfig);
        $explodedata = explode('.',$this->getFile());
        $Name = $explodedata[0].'_'.$id;
        $fileName = $Name.'.'.$explodedata[1];
        $this->getIoAdapter()->streamOpen($fileName, $mode);

        $this->_fileSize = 0;

        return $this;
    }
}
?>
