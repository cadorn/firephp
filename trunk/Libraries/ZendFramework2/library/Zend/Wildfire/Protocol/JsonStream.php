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
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Wildfire_Exception */
require_once 'Zend/Wildfire/Exception.php';

/** Zend_Wildfire_PluginInterface */
require_once 'Zend/Wildfire/PluginInterface.php';

/** Zend_Json_Encoder */
require_once 'Zend/Json/Encoder.php';

/**
 * Encodes messages into the Wildfire JSON Stream Communication Protocol.
 * 
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Wildfire_Protocol_JsonStream
{
    /**
     * The protocol URI for this protocol
     */
    const PROTOCOL_URI = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.1';

    /**
     * All messages to be sent.
     * @var array
     */
    protected $_messages = array();

    /**
     * Record a message with the given data in the given structure
     * 
     * @param Zend_Wildfire_PluginInterface $plugin The plugin recording the message
     * @param string $structure The structure to be used for the data
     * @param array $data The data to be recorded
     * @return boolean Returns TRUE if message was recorded
     */
    public function recordMessage(Zend_Wildfire_PluginInterface $plugin, $structure, $data)
    {
        if(!isset($this->_messages[$structure])) {
            $this->_messages[$structure] = array();  
        }
        
        $uri = $plugin->getUri();
        
        if(!isset($this->_messages[$structure][$uri])) {
            $this->_messages[$structure][$uri] = array();  
        }
        
        $this->_messages[$structure][$uri][] = $this->_encode($data);
        return true;
    }

    /**
     * Remove all qued messages
     * 
     * @param Zend_Wildfire_PluginInterface $plugin The plugin for which to clear messages
     * @return boolean Returns TRUE if messages were present
     */
    public function clearMessages(Zend_Wildfire_PluginInterface $plugin)
    {
        if(!isset($this->_messages[$structure])) {
            return false;
        }
      
        $uri = $plugin->getUri();
      
        if(!isset($this->_messages[$structure][$uri])) {
            return false;
        }
        
        unset($this->_messages[$structure][$uri]);

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
     * Retrieves all formatted data ready to be sent by the channel.
     * 
     * @param Zend_Wildfire_ChannelInterface $channel The instance of the channel that will be transmitting the data
     * @return mixed Returns the data to be sent by the channel.
     */
    public function getPayload(Zend_Wildfire_ChannelInterface $channel)
    {
        if (!$channel instanceof Zend_Wildfire_Channel_HttpHeaders) {
            throw new Zend_Wildfire_Exception('The '.get_class($channel).' channel is not supported by the '.get_class($this).' protocol.');          
        }
        
        if (!$this->_messages) {
            return false;
        }
                
        $protocol_index = 1;
        $structure_index = 1;
        $plugin_index = 1;
        $message_index = 1;

        $payload = array();

        $payload[] = array('Protocol-'.$protocol_index, self::PROTOCOL_URI);
        
        foreach ($this->_messages as $structure_uri => $plugin_messages ) {
            
            $payload[] = array($protocol_index.'-Structure-'.$structure_index, $structure_uri);

            foreach ($plugin_messages as $plugin_uri => $messages ) {
                
                $payload[] = array($protocol_index.'-Plugin-'.$plugin_index, $plugin_uri);
          
                foreach( $messages as $message ) {
                  
                    foreach (explode("\n",chunk_split($message, 5000, "\n")) as $part) {

                        if ($part) {
                  
                            $payload[] = array($protocol_index.'-'.
                                               $structure_index.'-'.
                                               $plugin_index.'-'.
                                               $message_index,
                                               $part);
                            
                            $message_index++;
                            
                            if ($message_index > 99999) {
                                throw new Zend_Wildfire_Exception('Maximum number (99,999) of messages reached!');             
                            }
                        }
                    }
                }
                $plugin_index++;
            }
            $structure_index++;
        }
        
        $payload[] = array($protocol_index.'-Index', $message_index-1);

        return $payload;
    }

}

?>