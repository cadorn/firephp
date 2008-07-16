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
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Wildfire_ChannelInterface */
require_once 'Zend/Wildfire/ChannelInterface.php';

/** Zend_Wildfire_Exception */
require_once 'Zend/Wildfire/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once('Zend/Controller/Request/Abstract.php');

/** Zend_Controller_Response_Abstract */
require_once('Zend/Controller/Response/Abstract.php');

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Wildfire_Protocol_JsonStream */
require_once 'Zend/Wildfire/Protocol/JsonStream.php';

/**
 * Implements communication via HTTP request and response headers for Wildfire Protocols.
 * 
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Wildfire_Channel_HttpHeaders extends Zend_Controller_Plugin_Abstract implements Zend_Wildfire_ChannelInterface
{
    /**
     * The string to be used to prefix the headers.
     * @var string
     */
    protected static $_headerPrefix = 'X-WF-';
 
    /**
     * Singleton instance
     * @var Zend_Wildfire_Channel_HttpHeaders
     */
    protected static $_instance = null;
 
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
     * The protocol instances for this channel
     * @var array
     */
    protected $_protocols = null;
    
    /**
     * Initialize singleton instance.
     *
     * @param string $class OPTIONAL Subclass of Zend_Wildfire_Channel_HttpHeaders
     * @return Zend_Wildfire_Channel_HttpHeaders Returns the singleton Zend_Wildfire_Channel_HttpHeaders instance
     * @throws Zend_Wildfire_Exception
     */
    public static function init($class = null)
    {
        if (self::$_instance!==null) {
            throw new Zend_Wildfire_Exception('Singleton instance of Zend_Wildfire_Channel_HttpHeaders already exists!');
        }
        if ($class!==null) {
            if (!is_string($class)) {
                throw new Zend_Wildfire_Exception('Third argument is not a class string');
            }
            Zend_Loader::loadClass($class);
            self::$_instance = new $class();
            if (!self::$_instance instanceof Zend_Wildfire_Channel_HttpHeaders) {
                throw new Zend_Wildfire_Exception('Invalid class to third argument. Must be subclass of Zend_Wildfire_Channel_HttpHeaders.');
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
        $controller = Zend_Controller_Front::getInstance();

        $controller->registerPlugin($this);

        $this->_request = $controller->getRequest();
        $this->_response = $controller->getResponse();
    }

    /**
     * Get or create singleton instance
     * 
     * @return Zend_Wildfire_Channel_HttpHeaders
     */
    public static function getInstance()
    {  
        if (self::$_instance===null) {
            return self::init();               
        }
        return self::$_instance;
    }
    
    /**
     * Get the instance of a give protocol for this channel
     * 
     * @param string $uri The URI for the protocol
     * @return object Returns the protocol instance for the diven URI
     */
    public function getProtocol($uri)
    {
        if (!isset($this->_protocols[$uri])) {
            $this->_protocols[$uri] = $this->_initProtocol($uri);
        }
 
        return $this->_protocols[$uri];
    }
    
    /**
     * Initialize a new protocol
     * 
     * @param string $uri The URI for the protocol to be initialized
     * @return object Returns the new initialized protocol instance
     */
    protected function _initProtocol($uri)
    {
        switch ($uri) {
            case Zend_Wildfire_Protocol_JsonStream::PROTOCOL_URI;
                return new Zend_Wildfire_Protocol_JsonStream();
        }
        throw new Zend_Wildfire_Exception('Tyring to initialize unknown protocol for URI "'.$uri.'".');
    }
    
    
    /*
     * Zend_Wildfire_ChannelInterface 
     */

    /**
     * Determine if channel is ready.
     * 
     * @return boolean Returns TRUE if channel is ready.
     */
    public function isReady()
    {
        return ($this->_response->canSendHeaders() &&
                preg_match_all('/\s?FirePHP\/([\.|\d]*)\s?/si',
                               $this->_request->getHeader('User-Agent'),$m));
    }


    /*
     * Zend_Controller_Plugin_Abstract 
     */

    /**
     * Flush all data from all registered plugins and send all data to response headers.
     *
     * @param  Zend_Controller_Request_Abstract  $request  The controller request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->isReady()) {
            return;
        }

        foreach ( $this->_protocols as $protocol ) {

            $payload = $protocol->getPayload($this);
            
            if ($payload) {
                foreach( $payload as $message ) {

                    $this->_response->setHeader(self::$_headerPrefix.$message[0],
                                                $message[1], true);
                }
            }
        }
    }
}

?>