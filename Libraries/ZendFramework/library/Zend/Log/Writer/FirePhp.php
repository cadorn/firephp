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
     * Maps logging priorities to logging display styles
     * @var array
     */
    protected $_logStyleMap = array(Zend_Log::EMERG => Zend_Debug_FirePhp::ERROR,
                                    Zend_Log::EMERG => Zend_Debug_FirePhp::ERROR,
                                    Zend_Log::ALERT => Zend_Debug_FirePhp::ERROR,
                                    Zend_Log::CRIT => Zend_Debug_FirePhp::ERROR,
                                    Zend_Log::ERR => Zend_Debug_FirePhp::ERROR,
                                    Zend_Log::WARN => Zend_Debug_FirePhp::WARN,
                                    Zend_Log::NOTICE => Zend_Debug_FirePhp::INFO,
                                    Zend_Log::INFO => Zend_Debug_FirePhp::INFO,
                                    Zend_Log::DEBUG => Zend_Debug_FirePhp::LOG);
    
    /**
     * The default logging style for un-mapped priorities
     * @var string
     */    
    protected $_defaultLogStyle = Zend_Debug_FirePhp::LOG;
    
    /**
     * Set the default log style for un-mapped priorities
     * 
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */    
    public function setDefaultLogStyle($style)
    {
        $previous = $this->_defaultLogStyle;
        $this->_defaultLogStyle = $style;
        return $previous;
    }
    
    /**
     * Map a logging priority to a logging display style
     * 
     * @param int $priority The logging priority
     * @param string $style The logging display style
     * @return string|boolean The previous logging display style if defined or TRUE otherwise
     */
    public function mapLogStyle($priority, $style)
    {
        $previous = true;
        if (array_key_exists($priority,$this->_logStyleMap)) {
            $previous = $this->_logStyleMap[$priority];
        }
        $this->_logStyleMap[$priority] = $style;
        return $previous;
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
     * Log a message to FirePHP.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        if (array_key_exists($event['priority'],$this->_logStyleMap)) {
            $type = $this->_logStyleMap[$event['priority']];
        } else {
            $type = $this->_defaultLogStyle;
        }
        Zend_Debug_FirePhp::getInstance()->fire($event['message'], null, $type);
    }
}
