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
 * @version    $Id: VersionTest.php 8064 2008-02-16 10:58:39Z thomas $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Wildfire_FirebugLogWriter */
require_once 'Zend/Wildfire/FirebugLogWriter.php';

/** Zend_Wildfire_Channel_HttpHeaders */
require_once 'Zend/Wildfire/Channel/HttpHeaders.php';

/** Zend_Wildfire_Plugin_FirePhp */
require_once 'Zend/Wildfire/Plugin/FirePhp.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';

/** Zend_Controller_Response_Http */
require_once 'Zend/Controller/Response/Http.php';

/** Zend_Controller_Front **/
require_once 'Zend/Controller/Front.php';

/** Zend_Json_Decoder */
require_once 'Zend/Json/Decoder.php';

/**
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage FirePhp
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Wildfire_FirebugLogWriterTest extends PHPUnit_Framework_TestCase
{
  
    protected $_controller = null;
    protected $_request = null;
    protected $_response = null;
    protected $_writer = null;
    protected $_logger = null;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Wildfire_FirebugLogWriterTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');
    }

    public function tearDown()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();
    }
    
    protected function _setupWithFrontController()
    {
        $this->_request = new Zend_Wildfire_FirebugLogWriterTest_Request();
        $this->_response = new Zend_Wildfire_FirebugLogWriterTest_Reponse();
        $this->_controller = Zend_Controller_Front::getInstance();
        $this->_controller->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files')
                          ->setRequest($this->_request)
                          ->setResponse($this->_response)
                          ->setParam('noErrorHandler', true)
                          ->setParam('noViewRenderer', true)
                          ->throwExceptions(false);

        $this->_writer = new Zend_Wildfire_FirebugLogWriter();
        $this->_logger = new Zend_Log($this->_writer);

        $this->_request->setUserAgentExtensionEnabled(true);
    }
    
    protected function _setupWithoutFrontController()
    {
        $this->_request = new Zend_Wildfire_FirebugLogWriterTest_Request();
        $this->_response = new Zend_Wildfire_FirebugLogWriterTest_Reponse();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        $this->_writer = new Zend_Wildfire_FirebugLogWriter();
        $this->_logger = new Zend_Log($this->_writer);

        $this->_request->setUserAgentExtensionEnabled(true);
    }
    
    public function testIsReady1()
    {
        $this->_setupWithFrontController();
      
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertTrue($channel->isReady());

        $this->_request->setUserAgentExtensionEnabled(false);

        $this->assertFalse($channel->isReady());
    }
    
    public function testIsReady2()
    {
        $this->_setupWithoutFrontController();
      
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertTrue($channel->isReady());

        $this->_request->setUserAgentExtensionEnabled(false);

        $this->assertFalse($channel->isReady());
    }

    public function testSetFormatter()
    {
        $this->_setupWithoutFrontController();
        try {
            $this->_writer->setFormatter(null);
            $this->fail('Should not be able to setFormatter() on log writer');
        } catch (Exception $e) {
            // success
        }
    }

    public function testLogStyling()
    {
        $this->_setupWithoutFrontController();

        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            Zend_Wildfire_Plugin_FirePhp::LOG);                
        $this->assertEquals($this->_writer->setDefaultPriorityStyle(Zend_Wildfire_Plugin_FirePhp::WARN),
                            Zend_Wildfire_Plugin_FirePhp::LOG);
        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            Zend_Wildfire_Plugin_FirePhp::WARN);                
                            
        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            false);                
        $this->assertEquals($this->_writer->setPriorityStyle(9,Zend_Wildfire_Plugin_FirePhp::WARN),
                            true);
        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            Zend_Wildfire_Plugin_FirePhp::WARN);
        $this->assertEquals($this->_writer->setPriorityStyle(9,Zend_Wildfire_Plugin_FirePhp::LOG),
                            Zend_Wildfire_Plugin_FirePhp::WARN);
    }
    
    public function testBasicLogging1()
    {
        $this->_setupWithFrontController();
      
        $message = 'This is a log message!';
           
        $this->_logger->log($message, Zend_Log::INFO);

        $this->_controller->dispatch();
        
        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.1';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/0.1';
        $headers['X-Wf-1-1-1-1'] = '[{"Type":"INFO"},"This is a log message!"]';
        $headers['X-Wf-1-Index'] = '1';
        
        $this->assertTrue($this->_response->verifyHeaders($headers));
    }
    
    public function testBasicLogging2()
    {
        $this->_setupWithoutFrontController();
      
        $message = 'This is a log message!';
           
        $this->_logger->log($message, Zend_Log::INFO);
        
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();
        
        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.1';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/0.1';
        $headers['X-Wf-1-1-1-1'] = '[{"Type":"INFO"},"This is a log message!"]';
        $headers['X-Wf-1-Index'] = '1';
                
        $this->assertTrue($this->_response->verifyHeaders($headers));                
    }    
    
    public function testAdvancedLogging2()
    {
        $this->_setupWithoutFrontController();
      
        $message = 'This is a log message!';
        $label = 'Test Label';
        $table = array('Summary line for the table',
                       array(
                           array('Column 1', 'Column 2'),
                           array('Row 1 c 1',' Row 1 c 2'),
                           array('Row 2 c 1',' Row 2 c 2')
                       )
                      );

        
        $this->_logger->addPriority('TRACE', 8);
        $this->_logger->addPriority('TABLE', 9);
        $this->_writer->setPriorityStyle(8, 'TRACE');
        $this->_writer->setPriorityStyle(9, 'TABLE');
        
        $this->_logger->trace($message);
        $this->_logger->table($table);
        
        Zend_Wildfire_Plugin_FirePhp::send($message, $label);
        Zend_Wildfire_Plugin_FirePhp::send($message, $label, Zend_Wildfire_Plugin_FirePhp::DUMP);
        
        try {
          throw new Exception('Test Exception');
        } catch (Exception $e) {
          Zend_Wildfire_Plugin_FirePhp::send($e);
        }

        try {
            Zend_Wildfire_Plugin_FirePhp::send($message, $label, 'UNKNOWN');
            $this->fail('Should not be able to log with undefined log style');
        } catch (Exception $e) {
            // success
        }
           
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $messages = array(Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array(1=>'[{"Type":"TABLE"},["Summary line for the table",[["Column 1","Column 2"],["Row 1 c 1"," Row 1 c 2"],["Row 2 c 1"," Row 2 c 2"]]]]',
                                      2=>'[{"Type":"LOG"},["Test Label","This is a log message!"]]')),
                          Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_DUMP=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array('{"Test Label":"This is a log message!"}')));
        
        $qued_messages = $protocol->getMessages();
        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0]);
        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][3]);

        $this->assertEquals(serialize($qued_messages),
                            serialize($messages));
    }    
    
        

    
    public function testFirePhpPluginInstanciation()
    {
        $this->_setupWithoutFrontController();
        try {
            Zend_Wildfire_Plugin_FirePhp::getInstance();
            Zend_Wildfire_Plugin_FirePhp::init(null);
            $this->fail('Should not be able to re-initialize');
        } catch (Exception $e) {
            // success
        }
    }
    
    public function testFirePhpPluginEnablement()
    {
        $this->_setupWithoutFrontController();
        
        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->assertFalse($protocol->getMessages());
        
        $this->assertTrue($firephp->getEnabled());
        
        $this->assertTrue($firephp->send('Hello World'));
        
        $messages = array(Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array('[{"Type":"LOG"},"Hello World"]')));
        
        $this->assertEquals(serialize($protocol->getMessages()),
                            serialize($messages));
        
        $this->assertTrue($firephp->setEnabled(false));

        $this->assertFalse($firephp->send('Hello World'));
        
        $this->assertFalse($protocol->getMessages());
    }
    
    public function testChannelInstanciation()
    {
        $this->_setupWithoutFrontController();
        try {
            Zend_Wildfire_Channel_HttpHeaders::getInstance();
            Zend_Wildfire_Channel_HttpHeaders::init(null);
            $this->fail('Should not be able to re-initialize');
        } catch (Exception $e) {
            // success
        }
    }
    
    public function testChannelFlush()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertFalse($channel->flush());

        Zend_Wildfire_Plugin_FirePhp::send('Hello World');

        $this->assertTrue($channel->flush());
        
        $this->_request->setUserAgentExtensionEnabled(false);
        
        $this->assertFalse($channel->flush());
    }
    
    public function testFirePhpPluginSubclass()
    {
      
        $firephp = Zend_Wildfire_Plugin_FirePhp::init('Zend_Wildfire_FirebugLogWriterTest_FirePhpPlugin');
      
        $this->assertEquals(get_class($firephp),
                            'Zend_Wildfire_FirebugLogWriterTest_FirePhpPlugin');
                            
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();

        try {
            Zend_Wildfire_Plugin_FirePhp::init('Zend_Wildfire_FirebugLogWriterTest_Request');
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }
        
        $this->assertNull(Zend_Wildfire_Plugin_FirePhp::getInstance(true));
                            
        try {
            Zend_Wildfire_Plugin_FirePhp::init(array());
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }
                            
        $this->assertNull(Zend_Wildfire_Plugin_FirePhp::getInstance(true));
    }
    
    public function testHttpHeadersChannelSubclass()
    {
      
        $firephp = Zend_Wildfire_Channel_HttpHeaders::init('Zend_Wildfire_FirebugLogWriterTest_HttpHeadersChannel');
      
        $this->assertEquals(get_class($firephp),
                            'Zend_Wildfire_FirebugLogWriterTest_HttpHeadersChannel');
                            
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();

        try {
            Zend_Wildfire_Channel_HttpHeaders::init('Zend_Wildfire_FirebugLogWriterTest_Request');
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }
        
        $this->assertNull(Zend_Wildfire_Channel_HttpHeaders::getInstance(true));
                            
        try {
            Zend_Wildfire_Channel_HttpHeaders::init(array());
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }
                            
        $this->assertNull(Zend_Wildfire_Channel_HttpHeaders::getInstance(true));
    }    
}

class Zend_Wildfire_FirebugLogWriterTest_FirePhpPlugin extends Zend_Wildfire_Plugin_FirePhp
{
}

class Zend_Wildfire_FirebugLogWriterTest_HttpHeadersChannel extends Zend_Wildfire_Channel_HttpHeaders
{
}

class Zend_Wildfire_FirebugLogWriterTest_Request extends Zend_Controller_Request_Http
{
    
    protected $_enabled = false;
    
    public function setUserAgentExtensionEnabled($enabled) {
        $this->_enabled = $enabled;
    }
    
    public function getHeader($header)
    {
        if ($header == 'User-Agent') {
            if ($this->_enabled) {
                return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
            } else {
                return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14';
            }         
        }
    }
}


class Zend_Wildfire_FirebugLogWriterTest_Reponse extends Zend_Controller_Response_Http
{

    public function verifyHeaders($headers)
    {

        $response_headers = $this->getHeaders();
        if (!$response_headers) {
            return false;
        }

        $keys1 = array_keys($headers);
        sort($keys1);
        $keys1 = serialize($keys1);

        $keys2 = array();
        foreach ($response_headers as $header ) {
            $keys2[] = $header['name'];
        }
        sort($keys2);
        $keys2 = serialize($keys2);

        if ($keys1 != $keys2) {
//            var_dump($keys1);
//            var_dump($keys2);
            return false;
        }

        $values1 = array_values($headers);
        sort($values1);
        $values1 = serialize($values1);

        $values2 = array();
        foreach ($response_headers as $header ) {
            $values2[] = $header['value'];
        }
        sort($values2);
        $values2 = serialize($values2);

        if ($values1 != $values2) {
//            var_dump($values1);
//            var_dump($values2);
            return false;
        }

        return true;
    }

}



// Call Zend_Wildfire_FirebugLogWriterTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Wildfire_FirebugLogWriterTest::main") {
    Zend_Wildfire_FirebugLogWriterTest::main();
}
