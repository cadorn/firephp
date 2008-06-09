<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007 Christoph Dorn
 * 
 * FirePHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FirePHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with FirePHP.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * ***** END LICENSE BLOCK ***** */

require_once 'Zend/Controller/Action.php';

require_once 'Zend/Exception.php';



/*
 * @copyright  Copyright (C) 2007 Christoph Dorn
 * @license    http://www.gnu.org/licenses/lgpl.html
 * @author     Jean-Marc Fontaine
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */

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
                
        FirePhp_Debug::fb(array('2 SQL queries took 0.06 seconds',array(
           array('SQL Statement','Time','Result'),
           array('SELECT * FROM Foo','0.02',array('row1','row2')),
           array('SELECT * FROM Bar','0.04',array('row1','row2'))
          )),FirePHP::TABLE);        
        
        /* Will show only in "Server" tab for the request */
        FirePhp_Debug::fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);


        FirePhp_Debug::log('Var1');
        FirePhp_Debug::log('Var1', 'Var2');
        FirePhp_Debug::log('Var1', 'Var2', 'Var3');


        
        /* Run some SQL so we can log the queries */

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


        /* Dump a variable to the "Server" tab of the request. */        
        FirePhp_Debug::dump('DummyLabel', 'Dummy string with a label');
        
        /* Log an exception */
//        throw new Zend_Exception('Zend Test Exception');
        
    }
}