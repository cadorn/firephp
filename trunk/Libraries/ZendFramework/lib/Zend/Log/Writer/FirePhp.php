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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** Zend_Debug_FirePhp */
require_once 'Zend/Debug/FirePhp.php';

/**
 * Writes log messages to the Firebug Console via FirePHP.
 * 
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log_Writer_FirePhp extends Zend_Log_Writer_Abstract
{

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
     * Log a message to FirePHP.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        switch($event['priority']) {
            case Zend_Log::EMERG:
            case Zend_Log::ALERT:
            case Zend_Log::CRIT:
            case Zend_Log::ERR:
                $type = Zend_Debug_FirePhp::ERROR;
                break;
            case Zend_Log::WARN:
                $type = Zend_Debug_FirePhp::WARN;
                break;
            case Zend_Log::NOTICE:
            case Zend_Log::INFO:
                $type = Zend_Debug_FirePhp::INFO;
                break;
            case Zend_Log::DEBUG:
            default:
                $type = Zend_Debug_FirePhp::LOG;
                break;
        }
        
        if($event['message'] instanceof Exception) {
          $type = Zend_Debug_FirePhp::EXCEPTION;
        }
        
        Zend_Debug_FirePhp::getInstance()->fire($event['message'], null, $type);
    }
}
