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
 * @copyright  Copyright (c) 2008 FirePHP (http://www.firephp.org)
 * @author     Christoph Dorn <christoph@christophdorn.com>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    0.1
 */

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** Zend_Json */
require_once 'Zend/Json.php';


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
 * @copyright  Copyright (c) 2008 FirePHP (http://www.firephp.org)
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
class Zend_Log_Writer_FirePHP extends Zend_Log_Writer_Abstract
{
    /**
     * Flag indicating if FirePHP extension was detected
     *
     * @var boolean
     */
    private $_firephpDetected = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        /* Check if FirePHP is installed on client */
        if(preg_match_all('/\sFirePHP\/([\.|\d]*)\s?/si',$_SERVER['HTTP_USER_AGENT'],$m) &&
           version_compare($m[1][0],'0.0.6','>=')) {
          $this->_firephpDetected = true;
        }
    }

    /**
     * Formatting is not possible on this writer
     *
     * @return void
     */
    public function setFormatter($formatter)
    {
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
        
        if(!$this->_firephpDetected)
        {
            return;
        }
        if (headers_sent($filename, $linenum))
        {
            throw new Zend_Log_Exception('Headers already sent in '.$filename.' on line '.$linenum.'. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.');
        }       
      
        $type = 'log';

        switch($event['priority'])
        {
          case Zend_Log::EMERG:
          case Zend_Log::ALERT:
          case Zend_Log::CRIT:
          case Zend_Log::ERR:
            $type = 'error';
            break;
          case Zend_Log::WARN:
            $type = 'warn';
            break;
          case Zend_Log::NOTICE:
          case Zend_Log::INFO:
            $type = 'info';
            break;
          case Zend_Log::DEBUG:
          default:
            $type = 'log';
            break;
        }     

      	header('X-FirePHP-Data-100000000001: {');
      	header('X-FirePHP-Data-300000000001: "FirePHP.Firebug.Console":[');
      	header('X-FirePHP-Data-399999999999: ["__SKIP__"]],');
      	header('X-FirePHP-Data-999999999999: "__SKIP__":"__SKIP__"}');
      
      	$msg = '["'.$type.'",'.Zend_Json::encode($event['message'],true).'],';
       
      	foreach( explode("\n",chunk_split($msg, 5000, "\n")) as $part )
        {
      		  $mt = explode(' ',microtime());
      		  $mt = substr($mt[1],7).substr($mt[0],2);
      
      		  header('X-FirePHP-Data-3'.$mt.': '.$part);
      	}
    }

}
