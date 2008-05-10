<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   FirePhp
 * @package    FirePhp_Core
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */

/** Zend_Controller_Request_Abstract */
require_once('Zend/Controller/Request/Abstract.php');

/** Zend_Controller_Response_Abstract */
require_once('Zend/Controller/Response/Abstract.php');

/** FirePHPCore/FirePHP.class.php */
require_once('FirePHPCore/FirePHP.class.php');

/*
 * @category   FirePhp
 * @package    FirePhp_Core
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
class FirePhp_Core extends FirePHP {
  
  private static $request = null;
  private static $response = null;
  
  public static function init(Zend_Controller_Request_Abstract $request = null,
                              Zend_Controller_Response_Abstract $response = null) {
    self::$request = $request;
    self::$response = $response;
    return self::$instance = new self();
  }
  
  protected function setHeader($Name, $Value) {
    if(self::$response===null) {
      return parent::setHeader($Name, $Value);
    }
    return self::$response->setHeader($Name, $Value, true);
  }

  protected function getUserAgent() {
    if(self::$request===null) {
      return parent::getUserAgent();
    }
    return self::$request->getServer('HTTP_USER_AGENT');
  }

  protected function newException($Message) {
    require_once 'Zend/Exception.php';
    return new Zend_Exception($Message);
  }
  
}

?>