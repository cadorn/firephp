<?php

require_once 'Zend/Controller/Action.php';

require_once 'Zend/Exception.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {

        /*
         * FirePhp_Log_Writer_FirePhp
         */

        $logger = Zend_Registry::get('logger');

        $logger->log('Emergency: system is unusable', Zend_Log::EMERG);
        $logger->log('Alert: action must be taken immediately', Zend_Log::ALERT);
        $logger->log('Critical: critical conditions', Zend_Log::CRIT);
        $logger->log('Error: error conditions', Zend_Log::ERR);
        $logger->log('Warning: warning conditions', Zend_Log::WARN);
        $logger->log('Notice: normal but significant condition', Zend_Log::NOTICE);
        $logger->log('Informational: informational messages', Zend_Log::INFO);
        $logger->log('Debug: debug messages', Zend_Log::DEBUG);
        $logger->log(array('$_SERVER',$_SERVER), Zend_Log::DEBUG);
        
        
        
        /*
         * FirePhp_Debug
         */
        
        FirePhp_Debug::fb('Hello World'); /* Defaults to FB_LOG */
        
        FirePhp_Debug::fb('Log message'  ,FirePHP::LOG);
        FirePhp_Debug::fb('Info message' ,FirePHP::INFO);
        FirePhp_Debug::fb('Warn message' ,FirePHP::WARN);
        FirePhp_Debug::fb('Error message',FirePHP::ERROR);
        
        FirePhp_Debug::fb('Message with label','Label',FirePHP::LOG);
        
        FirePhp_Debug::fb(array('key1'=>'val1',
                                'key2'=>array(array('v1','v2'),'v3')),
                          'TestArray',FirePHP::LOG);
        
        function test($Arg1) {
          throw new Exception('Test Exception');
        }
        try {
          test(array('Hello'=>'World'));
        } catch(Exception $e) {
          /* Log exception including stack trace & variables */
          FirePhp_Debug::fb($e);
        }
        
        /* Will show only in "Server" tab for the request */
        FirePhp_Debug::fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);



        FirePhp_Debug::log('Var1');
        FirePhp_Debug::log('Var1', 'Var2');
        FirePhp_Debug::log('Var1', 'Var2', 'Var3');

        
        FirePhp_Debug::dump('DummyLabel', 'Dummy string with a label');
        
        
        throw new Zend_Exception('Zend Test Exception');
        
    }
}