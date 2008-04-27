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
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Christoph Dorn (http://www.firephp.org)
 * @author     Christoph Dorn <christoph@christophdorn.com>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    0.1
 */

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** FirePhp_Core */
require_once 'FirePhp/Core.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Writes log messages to the Firebug Console via FirePHP.
 * See http://www.firephp.org/ for more information.
 * 
 * Usage:
 * 
 * $writer = new Zend_Log_Writer_FirePHP();
 * 
 * $logger = new Zend_Log($writer);
 * 
 * $logger->log('Emergency: system is unusable', Zend_Log::EMERG);
 * $logger->log('Alert: action must be taken immediately', Zend_Log::ALERT);
 * $logger->log('Critical: critical conditions', Zend_Log::CRIT);
 * $logger->log('Error: error conditions', Zend_Log::ERR);
 * $logger->log('Warning: warning conditions', Zend_Log::WARN);
 * $logger->log('Notice: normal but significant condition', Zend_Log::NOTICE);
 * $logger->log('Informational: informational messages', Zend_Log::INFO);
 * $logger->log('Debug: debug messages', Zend_Log::DEBUG);
 * 
 * $logger->log(array('$_SERVER',$_SERVER), Zend_Log::INFO);
 * 
 * 
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Christoph Dorn (http://www.firephp.org)
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
class FirePhp_Log_Writer_FirePhp extends Zend_Log_Writer_Abstract
{
    /**
     * Flag indicating if FirePHP extension was detected
     *
     * @var boolean
     */
    protected $_firePhpDetected = false;
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_firePhpDetected = FirePhp_Core::getInstance()->detectClientExtension();
    }

    /**
     * Formatting is not possible on this writer
     *
     * @return void
     */
    public function setFormatter($formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Write a message to FirePHP if the extension is installed.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        
        if(!$this->_firePhpDetected) {
            return;
        }
        if (headers_sent($filename, $line)) {
            $message = 'Headers already sent in ' . $filename . ' on line ' . $line .
                      '. Cannot send log data to FirePHP. You must have Output Buffering enabled.';
            
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception($message);
        }       
      
        switch($event['priority']) {
          case Zend_Log::EMERG:
          case Zend_Log::ALERT:
          case Zend_Log::CRIT:
          case Zend_Log::ERR:
            $type = FirePHP::ERROR;
            break;
          case Zend_Log::WARN:
            $type = FirePHP::WARN;
            break;
          case Zend_Log::NOTICE:
          case Zend_Log::INFO:
            $type = FirePHP::INFO;
            break;
          case Zend_Log::DEBUG:
          default:
            $type = FirePHP::LOG;
            break;
        }
        
        if($event['message'] instanceof Exception) {
          $type = FirePHP::EXCEPTION;
        }
        
        FirePhp_Core::getInstance()->fb($event['message'],$type);
    }
}
