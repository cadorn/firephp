<?php

require_once('Zend/Controller/Front.php');
require_once('FirePHP.class.php');

class FirePhp_Core extends FirePHP {
  
  private static function setHeader($Name, $Value) {
    $response = Zend_Controller_Front::getInstance()->getResponse();
    $response->setHeader($Name, $Value, true);
  }

  private static function getUserAgent() {
    return Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_USER_AGENT');
  }

  private static function newException($Message) {
    require_once 'Zend/Exception.php';
    return new Zend_Exception($Message);
  }
  
}

?>