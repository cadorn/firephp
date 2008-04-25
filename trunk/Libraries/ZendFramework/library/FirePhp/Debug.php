<?php

require_once 'Zend/Debug.php';
require_once 'FirePhp/Core.php';

class FirePhp_Debug extends Zend_Debug
{
  
  public static function fb() {
    $args = func_get_args();
    return call_user_func_array(array('FirePhp_Core','fb'),$args);
  }
  
  public static function log() {
    $args = func_get_args();
    return call_user_func_array(array('FirePhp_Core','log'),$args);
  }
  
  public static function dump($Key, $Variable) {
    return FirePhp_Core::dump($Key,$Variable);
  }

}