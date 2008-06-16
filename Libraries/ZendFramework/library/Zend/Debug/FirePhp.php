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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Debug */
require_once 'Zend/Debug.php';

/** Zend_Debug_FirePhp_Exception */
require_once 'Zend/Debug/FirePhp/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once('Zend/Controller/Request/Abstract.php');

/** Zend_Controller_Response_Abstract */
require_once('Zend/Controller/Response/Abstract.php');

/** Zend_Json_Encoder */
require_once 'Zend/Json/Encoder.php';

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Debug_FirePhp_Plugin_Interface **/
require_once 'Zend/Debug/FirePhp/Plugin/Interface.php';

/**
 * Primary class for communicating with FirePHP clients.
 * 
 * @category   Zend
 * @package    Zend_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Debug_FirePhp extends Zend_Controller_Plugin_Abstract
{
    /**
     * The string to be used to prefix the headers.
     * @var string
     */
    protected static $_headerPrefix = 'X-FirePHP-Data-';
 
    /**
     * The unique string ID identifying the Zend Framework server library.
     * @var string
     */
    protected static $_serverID = '0002';
 
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
     * Singleton instance
     * @var Zend_Debug_FirePhp
     */
    protected static $_instance = null;

    /**
     * Flag indicating whether FirePHP should send messages to the user-agent.
     * @var boolean
     */
    protected static $_enabled = false;

    /**
     * Instance of Zend_Controller_Request_Abstract
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;

    /**
     * Instance of Zend_Controller_Response_Abstract
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;
    
    /**
     * All headers to be sent.
     * @var array
     */
    protected $_headers = array();
    
    /**
     * All Zend_Debug_FirePhp_Plugin_Interface plugins that provide data for FirePHP
     * @var array
     */
    protected $_plugins = array();
        
    /**
     * Initialize singleton instance of FirePHP with a request and response object.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param Zend_Debug_FirePhp $class OPTIONAL Subclass for Zend_Debug_FirePhp instance
     * @return Zend_Debug_FirePhp Returns the singleton Zend_Debug_FirePhp instance
     * @throws Zend_Debug_FirePhp_Exception
     */
    public static function init(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                $class = null)
    {
      
        if (self::$_instance!==null) {
            throw new Zend_Debug_FirePhp_Exception('Singleton instance of Zend_Debug_FirePhp already exists!');
        }
        if (!$request || !$request instanceof Zend_Controller_Request_Abstract) {
            throw new Zend_Debug_FirePhp_Exception('Invalid request class');
        }
        if (!$response || !$response instanceof Zend_Controller_Response_Abstract) {
            throw new Zend_Debug_FirePhp_Exception('Invalid response class');
        }
        if ($class!==null) {
            if (!is_string($class)) {
                throw new Zend_Debug_FirePhp_Exception('Third argument is not a class string');
            }
            Zend_Loader::loadClass($class);
            self::$_instance = new $class();
            if (!self::$_instance instanceof Zend_Debug_FirePhp) {
                throw new Zend_Debug_FirePhp_Exception('Invalid class to third argument. Must be subclass of Zend_Debug_FirePhp.');
            }
        } else {
          self::$_instance = new self();
        }

        self::$_instance->_request = $request;
        self::$_instance->_response = $response;
        
        return self::$_instance;
    }
    
    /**
     * Singleton instance
     *
     * @return Zend_Debug_FirePhp
     * @throws Zend_Debug_FirePhp_Exception
     */
    public static function getInstance()
    {  
        if (self::$_instance===null) {
            throw new Zend_Debug_FirePhp_Exception('Singleton instance of Zend_Debug_FirePhp does not exist! You must call Zend_Debug_FirePhp::init(..) first.');
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
      $previous = self::$_enabled;
      self::$_enabled = $enabled;
      if (!self::$_enabled) {
          $this->_clearHeaders();
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
        return self::$_enabled;
    }
    
    /**
     * Add a plugin that provides data to FirePHP.
     * 
     * @param $plugin Zend_Debug_FirePhp_Plugin_Interface The plugin instance
     * @return boolean Returns TRUE if plugin was added, FALSE if already added.
     */
    public function addPlugin(Zend_Debug_FirePhp_Plugin_Interface $plugin)
    {
      if (in_array($plugin,$this->_plugins)) {
        return false;
      }
      $this->_plugins[] = $plugin;
      return true;
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
    public function fire($var, $label=null, $type=null)
    {
        if (!self::$_enabled ||
            !$this->_canSendHeaders() ||
            !$this->_isUserAgentExtensionInstalled()) {
            
            return false; 
        }

        if ($var instanceof Exception) {

          $var = array('Class'=>get_class($var),
                       'Message'=>$var->getMessage(),
                       'File'=>$var->getFile(),
                       'Line'=>$var->getLine(),
                       'Trace'=>$var->getTrace());

          $type = self::EXCEPTION;
          
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
            case self::TABLE:
            case self::DUMP:
                break;
            default:
                throw new Zend_Debug_FirePhp_Exception('Log type "'.$type.'" not recognized!');
                break;
        }
      
        if ($type == self::EXCEPTION) {
          $type = 'TRACE';
        }
        
        if ($type == self::DUMP) {
          
          return $this->_sendToRegister('FirePHP.Dump', $var, $label);
          
        } else {
          
          if ($label!=null) {
            $var = array($label,$var);
          }
          
          return $this->_sendToRegister('FirePHP.Firebug.Console', $var, $label, array('Type'=>$type));
        }
    }
    

    
    
    /**
     * Sends any native PHP type to any of the recognized registers.
     * The available registers are FirePHP.Dump and FirePHP.Firebug.Console
     * 
     * @param string $register The name of the target register
     * @param mixed $variable The PHP variable to be sent
     * @param string $key OPTIONAL The key to be used for the register. If not supplied variables are automatically indexed.
     * @param array $meta OPTIONAL The meta information to be sent along with the log message
     * @return boolean Returns TRUE if the variable was added to the response headers.
     * @throws Zend_Debug_FirePhp_Exception
     */
    protected function _sendToRegister($register, $variable, $key=null, $meta=null)
    {
        if (!$this->_headers)
        {
        	$this->_sendJSONMessage('100'.self::$_serverID.'00000', '{');
        	$this->_sendJSONMessage('210'.self::$_serverID.'00000', '"FirePHP.Dump":{');
        	$this->_sendJSONMessage('299'.self::$_serverID.'99999', '"__SKIP__":"__SKIP__"},');
        	$this->_sendJSONMessage('310'.self::$_serverID.'00000', '"FirePHP.Firebug.Console":[');
        	$this->_sendJSONMessage('399'.self::$_serverID.'99999', '["__SKIP__"]],');
        	$this->_sendJSONMessage('999'.self::$_serverID.'99999', '"__SKIP__":"__SKIP__"}');
        }

        switch ($register) {
            case 'FirePHP.Dump':
              
                if ($key==null) {
                    throw new Zend_Debug_FirePhp_Exception('You must supply a key.');
                }
                
              	return $this->_sendJSONMessage('220'.self::$_serverID,
                                              '"'.$key.'":'.$this->_encode($variable).',');
              
            case 'FirePHP.Firebug.Console':

                if ($meta==null || !is_array($meta) || !array_key_exists('Type',$meta)) {
                    throw new Zend_Debug_FirePhp_Exception('You must supply a "Type" in the meta information.');
                }
              
              	return $this->_sendJSONMessage('320'.self::$_serverID,
                                              '["'.$meta['Type'].'",'.$this->_encode($variable).'],');
                
            default:
                throw new Zend_Debug_FirePhp_Exception('Register of name "'.$register.'" is not recognized.');
                break;  
        }
        return false;
    }
    
    /**
     * Send a JSON formatted message to FirePHP
     * 
     * @param string $headerPrefix The prefix used for header names.
     * @param string $message A JSON formatted message.
     * @return boolean Return TRUE if message was added to response headers.
     * @throws Zend_Debug_FirePhp_Exception
     */
    protected function _sendJSONMessage($headerPrefix, $message)
    {
        foreach (explode("\n",chunk_split($message, 5000, "\n")) as $part) {
            if ($part) {
              
                $count = sizeof($this->_headers);
              
                if ($count >= 99999) {
                    throw new Zend_Debug_FirePhp_Exception('Maximum number (99,999) of log messages reached!');             
                }
                
                $name = $headerPrefix;
                if ( ( $len = strlen($name) ) < 12  && is_numeric($name) ) {
                  $name = $headerPrefix.str_pad((string)$count, 12-$len, STR_PAD_LEFT, '0');
                }
                
                if (!$this->_setHeader($name, $part)) {
                    return false;
                }
            }
      	}
        return true;        
    }

    /**
     * Use the JSON encoding scheme for the value specified
     *
     * @param mixed $value The value to be encoded
     * @return string  The encoded value
     */
    protected function _encode($value)
    {
        return Zend_Json_Encoder::encode($value);
    }
    
    /**
     * Can we send headers?
     *
     * @return boolean
     */
    protected function _canSendHeaders()
    {
        return $this->_response->canSendHeaders();
    }    
    
    /**
     * Set a header
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    protected function _setHeader($name, $value)
    {
        $this->_headers[$name] = $value;
        return true;
    }
    
    /**
     * Remove all headers to be sent
     * 
     * @return boolean Returns true
     */
    protected function _clearHeaders()
    {
        $this->_headers = array();
        return true;
    }
    
    /**
     * Check if FirePHP is installed on the user-agent
     * 
     * @return boolean Returns TRUE if FirePHP is installed on user-agent.
     */
    protected function _isUserAgentExtensionInstalled()
    {
      if (!preg_match_all('/\s?FirePHP\/([\.|\d]*)\s?/si',$this->_getUserAgent(),$m)) {
        return false;
      }
      return true;    
    }
    
    /**
     * Get the User-Agent
     *
     * @return string The user-agent string
     */
    protected function _getUserAgent()
    {
        return $this->_request->getHeader('User-Agent');
    }

    /**
     * Flush all data from all registered plugins and send all data to response headers.
     *
     * @param  Zend_Controller_Request_Abstract  $request  The controller request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!self::$_enabled) {
            return;
        }
        
        if ($this->_plugins) {
            foreach ( $this->_plugins as $plugin ) {
                $plugin->flush($this);
            }
        }
        $this->_plugins = array();

        if ($this->_headers) {
          
            ksort($this->_headers, SORT_NUMERIC); 
          
            foreach ($this->_headers as $name => $value) {
                $this->_response->setHeader(self::$_headerPrefix.$name, $value, true);
            }
        }
    }
}

?>