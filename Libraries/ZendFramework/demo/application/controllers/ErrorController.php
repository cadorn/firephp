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
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage FirePhp
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Debug */
require_once 'Zend/Debug.php';

/** Zend_Debug_FirePhp_Exception */
require_once 'Zend/Debug/FirePhp/Exception.php';

/**
 * A sample error controller.
 *
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage FirePhp
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        /*
         * Make sure we don't log FirePHP exceptions. If we do we will create an infinite loop.
         */
        if($errors->exception instanceof Zend_Debug_FirePhp_Exception) {
          
            if(ini_get('display_errors')) {
              print 'We caught a Zend_Debug_FirePhp_Exception!'."\n";
              
              Zend_Debug::dump($errors->exception);
            } else {
              
              /* TODO: In your production environment log the exception somewhere else. */
            }
            exit;
            
        } else {
          
            Zend_Registry::get('logger')->err($errors->exception);
            
            /* 
             * OR
             * 
             * Zend_Debug::fire($errors->exception);
             * 
             */
        }
    }
}

