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
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Wildfire_Exception */
require_once 'Zend/Wildfire/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once('Zend/Controller/Request/Abstract.php');

/** Zend_Controller_Response_Abstract */
require_once('Zend/Controller/Response/Abstract.php');

/** Zend_Wildfire_Channel_HttpHeaders */
require_once 'Zend/Wildfire/Channel/HttpHeaders.php';

/** Zend_Wildfire_Protocol_JsonStream */
require_once 'Zend/Wildfire/Protocol/JsonStream.php';

/** Zend_Wildfire_Plugin_Interface */
require_once 'Zend/Wildfire/Plugin/Interface.php';

/**
 * Primary class for communicating with the FirePHP Firefox Extension.
 * 
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Wildfire_Plugin_FirePhp implements Zend_Wildfire_Plugin_Interface
{
    /**
     * Plain log style.
     */
    const LOG = 'LOG';
    
    /**
     * Information style.
     */
    const INFO = 'INFO';
    
    /**
     * Warning style.
     */
    const WARN = 'WARN';
    
    /**
     * Error style that increments Firebug's error counter.
     */
    const ERROR = 'ERROR';
    
    /**
     * Trace style showing message and expandable full stack trace.
     */
    const TRACE = 'TRACE';
    
    /**
     * Exception style showing message and expandable full stack trace.
     * Also increments Firebug's error counter.
     */
    const EXCEPTION = 'EXCEPTION';
    
    /**
     * Table style showing summary line and expandable table
     */
    const TABLE = 'TABLE';

    /**
     * Dump variable to Server panel in Firebug Request Inspector
     */
    const DUMP = 'DUMP';
  
    /**
     * The plugin URI for this plugin
     */
    const PLUGIN_URI = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6';
    
    /**
     * The protocol URI for this plugin
     */
    const PROTOCOL_URI = Zend_Wildfire_Protocol_JsonStream::PROTOCOL_URI;
    
    /**
     * The structure URI for the Dump structure
     */
    const STRUCTURE_URI_DUMP = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1';

    /**
     * The structure URI for the Firebug Console structure
     */
    const STRUCTURE_URI_FIREBUGCONSOLE = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
  
    /**
     * Singleton instance
     * @var Zend_Wildfire_Plugin_FirePhp
     */
    protected static $_instance = null;

    /**
     * Flag indicating whether FirePHP should send messages to the user-agent.
     * @var boolean
     */
    protected $_enabled = true;
    
    /**
     * The channel via which to send the encoded messages.
     * @var Zend_Wildfire_Channel_Interface
     */
    protected $_channel = null;
    
    /**
     * Create singleton instance.
     *
     * @param string $class OPTIONAL Subclass of Zend_Wildfire_Plugin_FirePhp
     * @return Zend_Wildfire_Plugin_FirePhp Returns the singleton Zend_Wildfire_Plugin_FirePhp instance
     * @throws Zend_Wildfire_Exception
     */
    public static function init($class = null)
    {
      
        if (self::$_instance!==null) {
            throw new Zend_Wildfire_Exception('Singleton instance of Zend_Wildfire_Plugin_FirePhp already exists!');
        }
        if ($class!==null) {
            if (!is_string($class)) {
                throw new Zend_Wildfire_Exception('Third argument is not a class string');
            }
            Zend_Loader::loadClass($class);
            self::$_instance = new $class();
            if (!self::$_instance instanceof Zend_Wildfire_Plugin_FirePhp) {
                throw new Zend_Wildfire_Exception('Invalid class to third argument. Must be subclass of Zend_Wildfire_Plugin_FirePhp.');
            }
        } else {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Constructor
     * @return void
     */
    protected function __construct()
    {
        $this->_channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
    }

    /**
     * Get or create singleton instance
     * 
     * @return Zend_Debug_FirePhp
     */
    public static function getInstance()
    {  
        if (self::$_instance===null) {
            return self::init();               
        }
        return self::$_instance;
    }
    
    /**
     * Destroys the singleton instance
     *
     * Primarily used for testing.
     *
     * @return void
     */
    public static function destroyInstance()
    {
        self::$_instance = null;
    }    
    
    /**
     * Enable or disable sending of messages to user-agent.
     * If disabled all headers to be sent will be removed.
     * 
     * @param boolean $enabled Set to TRUE to enable sending of messages. 
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        if (!$this->_enabled) {
            $this->_channel->getProtocol(self::PROTOCOL_URI)->clearMessages($this);
        }
        return $previous;
    }
    
    /**
     * Determine if logging to user-agent is enabled.
     * 
     * @return boolean Returns TRUE if logging is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }
            
    /**
     * Logs variables to the Firebug Console
     * via HTTP response headers and the FirePHP Firefox Extension.
     *
     * @param  mixed  $var   The variable to log.
     * @param  string  $label OPTIONAL Label to prepend to the log event.
     * @param  string  $type  OPTIONAL Type specifying the style of the log event.
     * @return boolean Returns TRUE if the variable was added to the response headers.
     * @throws Zend_Debug_FirePhp_Exception
     */
    public static function send($var, $label=null, $type=null)
    {
        if (!self::$_instance->_enabled ||
            !self::$_instance->_channel->isReady()) {
            return false; 
        }

        if ($var instanceof Exception) {

            $var = array('Class'=>get_class($var),
                         'Message'=>$var->getMessage(),
                         'File'=>$var->getFile(),
                         'Line'=>$var->getLine(),
                         'Type'=>'throw',
                         'Trace'=>$var->getTrace());
  
            $type = self::EXCEPTION;
          
        } else
        if ($type==self::TRACE) {
            
            $trace = debug_backtrace();
            if(!$trace) return false;
            for( $i=0 ; $i<sizeof($trace) ; $i++ ) {
/*
                if(isset($trace[$i]['class']) &&
                   $trace[$i]['class']=='Zend_Debug' &&
                   $trace[$i]['function']=='_dispatchToMethodHandlers' &&
                   substr($trace[$i]['file'],-14,14)=='Zend/Debug.php') {

                    $i++;
                    break;
                }
*/
            }

            if($i==sizeof($trace)) {
                $i = 0;
            }

            $var = array('Class'=>$trace[$i]['class'],
                         'Type'=>$trace[$i]['type'],
                         'Function'=>$trace[$i]['function'],
                         'Message'=>$trace[$i]['args'][0],
                         'File'=>$trace[$i]['file'],
                         'Line'=>$trace[$i]['line'],
                         'Args'=>$trace[$i]['args'],
                         'Trace'=>array_splice($trace,$i+1));
        } else {
            if ($type===null) {
                $type = self::LOG;
            }
        }

        switch ($type) {
            case self::LOG:
            case self::INFO:
            case self::WARN:
            case self::ERROR:
            case self::EXCEPTION:
            case self::TRACE:
            case self::TABLE:
            case self::DUMP:
                break;
            default:
                throw new Zend_Wildfire_Exception('Log type "'.$type.'" not recognized!');
                break;
        }
        
        if ($type == self::DUMP) {
          
          return self::$_instance->_recordMessage(self::STRUCTURE_URI_DUMP,
                                                  array('key'=>$label,
                                                        'data'=>$var));
          
        } else {
          
          if ($label!=null) {
            $var = array($label,$var);
          }
          
          return self::$_instance->_recordMessage(self::STRUCTURE_URI_FIREBUGCONSOLE,
                                                  array('data'=>$var,
                                                        'meta'=>array('Type'=>$type)));
        }
    }
    
    
    /**
     * Record a message with the given data in the given structure
     * 
     * @param string $structure The structure to be used for the data
     * @param array $data The data to be recorded
     * @return boolean Returns TRUE if message was recorded
     */
    protected function _recordMessage($structure, $data)
    {
        switch($structure) {

            case self::STRUCTURE_URI_DUMP:
            
                if (!isset($data['key'])) {
                    throw new Zend_Wildfire_Exception('You must supply a key.');
                }
                if (!isset($data['data'])) {
                    throw new Zend_Wildfire_Exception('You must supply data.');
                }

                if(!isset($this->_messages[$structure])) {
                    $this->_messages[$structure] = array();
                }
                
                return $this->_channel->getProtocol(self::PROTOCOL_URI)->
                           recordMessage($this,
                                         $structure,
                                         array($data['key']=>$data['data']));
                
            case self::STRUCTURE_URI_FIREBUGCONSOLE:
            
                if (!isset($data['meta']) ||
                    !is_array($data['meta']) ||
                    !array_key_exists('Type',$data['meta'])) {
                      
                    throw new Zend_Wildfire_Exception('You must supply a "Type" in the meta information.');
                }
                if (!isset($data['data'])) {
                    throw new Zend_Wildfire_Exception('You must supply data.');
                }
              
                return $this->_channel->getProtocol(self::PROTOCOL_URI)->
                           recordMessage($this,
                                         $structure,
                                         array($data['meta'],
                                               $data['data']));

            default:
                throw new Zend_Wildfire_Exception('Structure of name "'.$structure.'" is not recognized.');
                break;  
        }
        return false;      
    }

    
    
    /*
     * Zend_Wildfire_Plugin_Interface
     */

    /**
     * Get the unique indentifier for this plugin.
     * 
     * @return string Returns the URI of the plugin.
     */
    public function getUri()
    {
        return self::PLUGIN_URI;
    }
}

?>