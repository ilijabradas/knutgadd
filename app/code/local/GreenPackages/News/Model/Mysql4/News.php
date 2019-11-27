<?php

class GreenPackages_News_Model_Mysql4_News extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the news_id refers to the key field in your database table.
        $this->_init('news/news', 'news_id');
    }
	
	/**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
	
	public function urls_amigables($url) { 

			// Tranformamos todo a minusculas 
			$url = strtolower($url); 

			//Rememplazamos caracteres especiales latinos 
			$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ'); 
			$repl = array('a', 'e', 'i', 'o', 'u', 'n'); 
			$url = str_replace ($find, $repl, $url); 

			// Añadimos los guiones 
			$find = array(' ', '&', '\r\n', '\n', '+'); 
			$url = str_replace ($find, '-', $url); 

			// Eliminamos y Reemplazamos demás caracteres especiales 
			$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/'); 
			$repl = array('', '-', ''); 
			$url = preg_replace ($find, $repl, $url); 

			return $url; 

	}


	public function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$amigable = $this->urls_amigables($object->getTitular());
		$object->setAmigable($amigable);	
		
		if(!$object->getIsMassupdate()){
			if (!$object->getFechaini()) {
				//$object->setFechaini(new Zend_Db_Expr('NULL'));
			}else{
				$fechaini = explode('/',$object->getFechaini());
				$object->setFechaini($this->formatDate($fechaini[1].'/'.$fechaini[0].'/'.$fechaini[2]));			
			}

			if (!$object->getFechafin()) {
				//$object->setFechafin(new Zend_Db_Expr('NULL'));
			}else{
				$fechafin = explode('/',$object->getFechafin());
				$object->setFechafin($this->formatDate($fechafin[1].'/'.$fechafin[0].'/'.$fechafin[2]));		
			}
		}
		return $this;
	}
	
	public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (strcmp($value, (int)$value) !== 0) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }


}
