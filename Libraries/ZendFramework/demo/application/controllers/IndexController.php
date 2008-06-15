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
 */

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/** Zend_Exception */
require_once 'Zend/Exception.php';

/**
 * Sample index controller.
 *
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage FirePhp
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        /*
         * Zend_Log_Writer_FirePhp
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
         * Zend_Debug
         */
        
        Zend_Debug::fire('Hello World'); /* Defaults to Zend_Debug_FirePhp::LOG */
        
        Zend_Debug::fire('Log message'  , Zend_Debug_FirePhp::LOG);
        Zend_Debug::fire('Log message'  , 'LOG');
        Zend_Debug::fire('Info message' , Zend_Debug_FirePhp::INFO);
        Zend_Debug::fire('Info message' , 'INFO');
        Zend_Debug::fire('Warn message' , Zend_Debug_FirePhp::WARN);
        Zend_Debug::fire('Warn message' , 'WARN');
        Zend_Debug::fire('Error message', Zend_Debug_FirePhp::ERROR);
        Zend_Debug::fire('Error message', 'ERROR');
        
        Zend_Debug::fire('Message with label','Label', Zend_Debug_FirePhp::LOG);
        Zend_Debug::fire('Message with label','Label', 'LOG');
        
        Zend_Debug::fire(array('key1'=>'val1',
                               'key2'=>array(array('v1','v2'),'v3')),
                         'TestArray', Zend_Debug_FirePhp::LOG);
        
        try {
            throw new Exception('Test Exception');
        } catch(Exception $e) {
            Zend_Debug::fire($e);
        }
                
        Zend_Debug::fire(array(
                           array('Column1','Column2','Column3'),
                           array('Row 1 Column 1','Row 1 Column 2',array('row1','row2')),
                           array('Row 2 Column 1','Row 2 Column 2',array('row1','row2'))
                         ),
                         'This is a Sample Table',
                         'TABLE');        

        Zend_Debug::fire(apache_request_headers(),'RequestHeaders',Zend_Debug_FirePhp::DUMP);

  
        /* Run some SQL so we can log the queries using Zend_Db_Profiler_FirePhp */

        $db = Zend_Registry::get('db');

        $db->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL  
                                    )');

        $db->insert('foo', array('id'=>1,'col1'=>'original'));

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->update('foo', array('col1'=>'new'), 'id = 1');

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->delete('foo', 'id = 1');

        $db->getConnection()->exec('DROP TABLE foo');
          
        
        /* Throw an exception so that or error controller can log it. */
        throw new Zend_Exception('Zend Test Exception');
        
    }
}