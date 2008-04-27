<?php

require_once('Zend/Controller/Front.php');
require_once('FirePHP.class.php');

class FirePhp_Core extends FirePHP {
  
  private static $request = null;
  private static $response = null;
  
  public static function init(Zend_Controller_Request_Abstract $request = null,
                              Zend_Controller_Response_Abstract $response = null) {
    self::$request = $request;
    self::$response = $response;
    self::$instance = new self();
  }
  
  protected function setHeader($Name, $Value) {
    return self::$response->setHeader($Name, $Value, true);
  }

  protected function getUserAgent() {
    return self::$request->getServer('HTTP_USER_AGENT');
  }

  protected function newException($Message) {
    require_once 'Zend/Exception.php';
    return new Zend_Exception($Message);
  }
  
}

?>