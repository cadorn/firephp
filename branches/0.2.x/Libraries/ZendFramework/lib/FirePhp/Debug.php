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
 * @package    FirePhp_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */

/** Zend_Debug */
require_once 'Zend/Debug.php';

/** FirePhp_Core */
require_once 'FirePhp/Core.php';

/*
 * @category   FirePhp
 * @package    FirePhp_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
class FirePhp_Debug extends Zend_Debug
{
  
  public static function fb() {
    $args = func_get_args();
    return call_user_func_array(array(FirePhp_Core::getInstance(),'fb'),$args);
  }
  
  public static function log() {
    $args = func_get_args();
    return call_user_func_array(array(FirePhp_Core::getInstance(),'log'),$args);
  }
  
  public static function dump($Key, $Variable) {
    return FirePhp_Core::getInstance()->dump($Key,$Variable);
  }

}