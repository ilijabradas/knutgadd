<?php 

class NWT_KCO_Exception extends Zend_Exception { //use Zend_Exception to manage $previous param for php 5.2

    protected $_redirect = null;

    public function __construct($message = null, $redirect = null, $code = 0, Exception $previous = null) {
        $this->_redirect = $redirect;
        return parent::__construct($message,$code,$previous);
    }

    public function getRedirect() {
        return $this->_redirect;
    }

    public function isReload() {
        return $this->_redirect == '*/*';
    }


}
